<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Módulos do site: menu principal, links úteis do footer, trilha de navegação.
 * Ajusta os módulos criados na instalação do Joomla em vez de duplicá-los.
 */
final class ModulesStep extends AbstractStep
{
    public function title(): string
    {
        return 'Módulos';
    }

    public function run(): void
    {
        // Menu principal: reaproveita o "Main Menu" da instalação
        $this->adoptCoreModule('mod:mainmenu', 'mod_menu', 'Menu principal', [
            'position'  => 'mainmenu',
            'showtitle' => 0,
        ], [
            'menutype'        => MenusStep::MAIN_MENU,
            'layout'          => 'hospital_intranet:main',
            'startLevel'      => 1,
            'endLevel'        => 2,
            'showAllChildren' => 1,
        ]);

        // Sem login no frontend (PLANO.md): o formulário de login sai do site
        $this->adoptCoreModule('mod:login', 'mod_login', 'Formulário de login (desativado)', [
            'published' => 0,
        ]);

        $this->adoptCoreModule('mod:breadcrumbs', 'mod_breadcrumbs', 'Trilha de navegação', [
            'position'  => 'breadcrumbs',
            'showtitle' => 0,
        ], [
            'showHere' => 0,
            'showHome' => 1,
            'homeText' => 'Início',
            'showLast' => 1,
        ]);

        $this->ensureModule('mod:footer-menu', [
            'title'     => 'Links úteis',
            'module'    => 'mod_menu',
            'position'  => 'footer-menu',
            'showtitle' => 1,
            'params'    => [
                'menutype'   => MenusStep::FOOTER_MENU,
                'layout'     => 'hospital_intranet:footer',
                'startLevel' => 1,
                'endLevel'   => 1,
            ],
        ]);
    }

    /**
     * Ajusta o módulo de site do tipo $module criado na instalação (o de menor id)
     * e marca com a nota, para não mexer de novo nas próximas execuções.
     */
    private function adoptCoreModule(string $key, string $module, string $label, array $columns, array $params = []): void
    {
        if ($this->findByNote('#__modules', $key, ['client_id' => 0])) {
            $this->exists($label);

            return;
        }

        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(['id', 'params']))
            ->from($this->db->quoteName('#__modules'))
            ->where($this->db->quoteName('module') . ' = :module')
            ->where($this->db->quoteName('client_id') . ' = 0')
            ->order($this->db->quoteName('id'))
            ->bind(':module', $module);

        $row = $this->db->setQuery($query, 0, 1)->loadObject();

        if (!$row) {
            $this->io->writeln("  <comment>!</comment> $label: módulo $module não encontrado, ignorado");

            return;
        }

        $merged = (new Registry($row->params))->merge(new Registry($params));

        $this->update('#__modules', (int) $row->id, $columns + [
            'title'  => $label,
            'note'   => $this->note($key),
            'params' => $merged->toString(),
        ]);

        $this->changed($label);
    }

    private function ensureModule(string $key, array $module): void
    {
        if ($this->findByNote('#__modules', $key, ['client_id' => 0])) {
            $this->exists($module['title']);

            return;
        }

        $this->save($this->model('com_modules', 'Module'), $module + [
            'id'         => 0,
            'client_id'  => 0,
            'note'       => $this->note($key),
            'published'  => 1,
            'access'     => 1,
            'language'   => '*',
            'content'    => '',
            'assignment' => 0,   // todas as páginas
            'assigned'   => [],
        ], $module['title']);

        $this->created($module['title']);
    }
}
