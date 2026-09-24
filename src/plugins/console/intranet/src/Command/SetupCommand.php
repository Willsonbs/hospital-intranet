<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Command;

use HospitalSantaAurora\Plugin\Console\Intranet\Setup\AclStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\CategoriesStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\DemoStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\FieldsStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\MenusStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\ModulesStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\PanelStep;
use HospitalSantaAurora\Plugin\Console\Intranet\Setup\TemplateStep;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

\defined('_JEXEC') or die;

/**
 * php cli/joomla.php intranet:setup
 *
 * Cria a estrutura da intranet. Idempotente: cada registro criado recebe a nota
 * "intranet:<chave>" e, se já existir, não é recriado nem sobrescrito.
 */
final class SetupCommand extends AbstractCommand
{
    protected static $defaultName = 'intranet:setup';

    protected function configure(): void
    {
        $this->setDescription('Cria a estrutura da intranet (template, categorias, campos, permissões, menus e módulos)');
        $this->addOption('demo', null, InputOption::VALUE_NONE, 'Cria também o conteúdo de demonstração (sistemas, notícias, documentos, avisos, eventos)');
        $this->setHelp(
            "Cria o que estiver faltando e mantém o que já existe.\n"
            . "Registros criados aqui têm a nota \"intranet:<chave>\" no painel: não altere essas notas."
        );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $io  = new SymfonyStyle($input, $output);
        $app = $this->getApplication();
        $db  = Factory::getContainer()->get(DatabaseInterface::class);

        $io->title('Intranet Hospital Santa Aurora — setup');

        // Os models do Joomla registram autor/permissões: executa como o 1º super usuário.
        // Também na sessão: partes do núcleo ainda usam Factory::getUser(), que lê de lá
        // (ex.: FieldsHelper::canEditFieldValue — sem isso os campos personalizados não são gravados).
        $user = $this->firstSuperUser($db);
        $app->loadIdentity($user);
        $app->getSession()->set('user', $user);

        $steps = [
            new TemplateStep($app, $db, $io),
            new CategoriesStep($app, $db, $io),
            new FieldsStep($app, $db, $io),
            new AclStep($app, $db, $io),
            new MenusStep($app, $db, $io),
            new ModulesStep($app, $db, $io),
            new PanelStep($app, $db, $io),
        ];

        if ($input->getOption('demo')) {
            $steps[] = new DemoStep($app, $db, $io);
        }

        try {
            foreach ($steps as $step) {
                $io->section($step->title());
                $step->run();
            }
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Estrutura da intranet pronta.');

        return Command::SUCCESS;
    }

    private function firstSuperUser(DatabaseInterface $db): User
    {
        $group = 8;
        $query = $db->getQuery(true)
            ->select('MIN(' . $db->quoteName('user_id') . ')')
            ->from($db->quoteName('#__user_usergroup_map'))
            ->where($db->quoteName('group_id') . ' = :group')
            ->bind(':group', $group, ParameterType::INTEGER);

        $id = (int) $db->setQuery($query)->loadResult();

        if (!$id) {
            throw new \RuntimeException('Nenhum super usuário encontrado.');
        }

        return new User($id);
    }
}
