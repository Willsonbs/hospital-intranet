<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Command;

use HospitalSantaAurora\Plugin\Console\Intranet\Setup\AclStep;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

\defined('_JEXEC') or die;

/**
 * php cli/joomla.php intranet:acl-report
 *
 * Mostra o que cada papel administrativo pode fazer, calculado pelo ACL do
 * Joomla (herança e negações incluídas). Útil para auditoria.
 */
final class AclReportCommand extends AbstractCommand
{
    protected static $defaultName = 'intranet:acl-report';

    protected function configure(): void
    {
        $this->setDescription('Mostra a matriz de permissões dos papéis administrativos');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $titles = [AclStep::GROUP_ADMIN, AclStep::GROUP_QUALIDADE, AclStep::GROUP_IMPRENSA];
        $query  = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__usergroups'))
            ->whereIn($db->quoteName('title'), $titles, ParameterType::STRING);
        $groups = $db->setQuery($query)->loadAssocList('title', 'id');

        if (\count($groups) !== 3) {
            $io->error('Papéis não encontrados. Rode intranet:setup antes.');

            return Command::FAILURE;
        }

        $categories = $db->setQuery(
            $db->getQuery(true)
                ->select($db->quoteName(['id', 'title', 'note']))
                ->from($db->quoteName('#__categories'))
                ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
                ->where($db->quoteName('level') . ' = 1')
                ->order($db->quoteName('lft'))
        )->loadObjectList();

        $checks = [
            'Entrar no painel'                => ['core.login.admin', null],
            'Configuração global'             => ['core.admin', null],
            'Usuários'                        => ['core.manage', 'com_users'],
            'Menus'                           => ['core.manage', 'com_menus'],
            'Módulos'                         => ['core.manage', 'com_modules'],
            'Categorias'                      => ['core.manage', 'com_categories'],
            'Campos (estrutura)'              => ['core.manage', 'com_fields'],
            'Artigos (lista)'                 => ['core.manage', 'com_content'],
            'Mídia: ver'                      => ['core.manage', 'com_media'],
            'Mídia: enviar'                   => ['core.create', 'com_media'],
            'Mídia: excluir'                  => ['core.delete', 'com_media'],
        ];

        foreach ($categories as $category) {
            foreach (['core.create' => 'criar', 'core.edit.state' => 'publicar', 'core.delete' => 'excluir'] as $action => $label) {
                $checks["$category->title: $label"] = [$action, 'com_content.category.' . $category->id];
            }
        }

        // Como em User::authorise(): quem tem core.admin na raiz (super usuário) pode tudo
        $isSuper = array_map(static fn ($id) => (bool) Access::checkGroup((int) $id, 'core.admin'), $groups);
        $rows    = [];

        foreach ($checks as $label => [$action, $asset]) {
            $row = [$label];

            foreach ($titles as $title) {
                $allowed = $isSuper[$title] || Access::checkGroup((int) $groups[$title], $action, $asset);
                $row[]   = $allowed ? '<info>sim</info>' : '<comment>—</comment>';
            }

            $rows[] = $row;
        }

        $io->title('Permissões dos papéis administrativos');
        $io->table(['Permissão', 'Administrador', 'Qualidade', 'Imprensa'], $rows);

        return Command::SUCCESS;
    }
}
