<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\CMS\Table\MenuType;

\defined('_JEXEC') or die;

/**
 * Menu principal (header) e menu "Links úteis" (footer).
 */
final class MenusStep extends AbstractStep
{
    public const MAIN_MENU   = 'mainmenu';
    public const FOOTER_MENU = 'footer';

    /** Ícones (Lucide) dos submenus de Sistemas, pela chave da categoria */
    private const SYSTEM_ICONS = [
        'assistenciais'    => 'stethoscope',
        'administrativos'  => 'briefcase',
        'recursos-humanos' => 'users',
        'financeiro'       => 'wallet',
        'ti'               => 'monitor',
        'bi-e-indicadores' => 'chart-column',
    ];

    public function title(): string
    {
        return 'Menus';
    }

    public function run(): void
    {
        $this->ensureMenuType(self::FOOTER_MENU, 'Links úteis', 'Links do rodapé');
        $this->renameHome();

        $contentId = $this->extensionId('com_content');
        $pageParams = ['show_page_heading' => 0, 'menu_show' => 1];

        // --- Menu principal ---------------------------------------------------
        $sistemas = $this->ensureItem('menu:sistemas', [
            'title'  => 'Sistemas',
            'alias'  => 'sistemas',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=categories&id=' . $this->categoryId('sistemas'),
            'component_id' => $contentId,
            'params' => $pageParams + ['show_base_description' => 0, 'maxLevelcat' => 1],
        ]);

        foreach (CategoriesStep::TREE['sistemas'][1] as $alias => $title) {
            $this->ensureItem('menu:sistemas/' . $alias, [
                'title'     => $title,
                'alias'     => $alias,
                'parent_id' => $sistemas,
                'type'      => 'component',
                'link'      => 'index.php?option=com_content&view=category&id=' . $this->categoryId('sistemas/' . $alias),
                'component_id' => $contentId,
                'params'    => $pageParams + ['menu_icon_css' => self::SYSTEM_ICONS[$alias] ?? ''],
            ], "Sistemas › $title");
        }

        $ramais = $this->ensureRamais();

        $eventos = $this->ensureItem('menu:eventos', [
            'title'  => 'Eventos',
            'alias'  => 'eventos',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=category&layout=blog&id=' . $this->categoryId('eventos'),
            'component_id' => $contentId,
            'params' => $pageParams,
        ]);

        $this->ensureItem('menu:protocolos', [
            'title'  => 'Protocolos',
            'alias'  => 'protocolos',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=category&id=' . $this->categoryId('biblioteca/protocolos'),
            'component_id' => $contentId,
            'params' => $pageParams,
        ]);

        $this->ensureItem('menu:noticias', [
            'title'  => 'Notícias',
            'alias'  => 'noticias',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=category&layout=blog&id=' . $this->categoryId('noticias'),
            'component_id' => $contentId,
            'params' => $pageParams,
        ]);

        $documentos = $this->ensureItem('menu:documentos', [
            'title'  => 'Documentos',
            'alias'  => 'documentos',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=category&id=' . $this->categoryId('biblioteca'),
            'component_id' => $contentId,
            'params' => $pageParams,
        ]);

        // Oculto nos menus: só dá endereços amigáveis aos avisos (/avisos/...)
        $this->ensureItem('menu:avisos', [
            'title'  => 'Avisos',
            'alias'  => 'avisos',
            'type'   => 'component',
            'link'   => 'index.php?option=com_content&view=category&layout=blog&id=' . $this->categoryId('avisos'),
            'component_id' => $contentId,
            'params' => ['show_page_heading' => 0, 'menu_show' => 0],
        ]);

        // --- Footer: atalhos (tipo "alias") para itens do menu principal ------
        foreach (['sistemas' => [$sistemas, 'Sistemas'], 'ramais' => [$ramais, 'Ramais'],
                  'documentos' => [$documentos, 'Documentos'], 'eventos' => [$eventos, 'Eventos']] as $key => [$target, $title]) {
            $this->ensureItem('menu:footer/' . $key, [
                'menutype' => self::FOOTER_MENU,
                'title'    => $title,
                // Aliases são únicos por nível em todos os menus; o link real vem do item de destino
                'alias'    => 'rodape-' . $key,
                'type'     => 'alias',
                'link'     => 'index.php?Itemid=',
                'params'   => ['aliasoptions' => $target, 'alias_redirect' => 0, 'menu_show' => 1],
            ], "Links úteis › $title");
        }
    }

    /**
     * Item "Ramais": aponta para o diretório (com_ramais). Instalações criadas antes do
     * componente tinham um item provisório do tipo URL, convertido aqui.
     */
    private function ensureRamais(): int
    {
        $componentId = $this->extensionId('com_ramais');
        $link        = 'index.php?option=com_ramais&view=ramais';

        if (!$componentId) {
            throw new \RuntimeException('Componente com_ramais não está instalado.');
        }

        $id = $this->ensureItem('menu:ramais', [
            'title'        => 'Ramais',
            'alias'        => 'ramais',
            'type'         => 'component',
            'link'         => $link,
            'component_id' => $componentId,
            'params'       => ['show_page_heading' => 0, 'menu_show' => 1, 'ordenacao' => 'setor'],
        ]);

        $type = $this->db->setQuery(
            $this->db->getQuery(true)
                ->select($this->db->quoteName('type'))
                ->from($this->db->quoteName('#__menu'))
                ->where($this->db->quoteName('id') . ' = ' . $id)
        )->loadResult();

        if ($type === 'url') {
            $this->update('#__menu', $id, [
                'type'         => 'component',
                'link'         => $link,
                'component_id' => $componentId,
                'params'       => json_encode(['show_page_heading' => 0, 'menu_show' => 1, 'ordenacao' => 'setor']),
            ]);
            $this->changed('Ramais: item de menu agora aponta para o diretório');
        }

        return $id;
    }

    private function ensureMenuType(string $menutype, string $title, string $description): void
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__menu_types'))
            ->where($this->db->quoteName('menutype') . ' = :menutype')
            ->bind(':menutype', $menutype);

        if ((int) $this->db->setQuery($query)->loadResult() > 0) {
            $this->exists("Menu \"$title\"");

            return;
        }

        $table = new MenuType($this->db);

        if (!$table->save(['menutype' => $menutype, 'title' => $title, 'description' => $description, 'client_id' => 0])) {
            throw new \RuntimeException("Falha ao criar o menu \"$title\": " . $table->getError());
        }

        $this->created("Menu \"$title\"");
    }

    /** O item "Home" criado na instalação vira "Início". */
    private function renameHome(): void
    {
        if ($this->findByNote('#__menu', 'menu:inicio')) {
            $this->exists('Início (página inicial)');

            return;
        }

        $menutype = self::MAIN_MENU;
        $query    = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__menu'))
            ->where($this->db->quoteName('home') . ' = 1')
            ->where($this->db->quoteName('client_id') . ' = 0')
            ->where($this->db->quoteName('menutype') . ' = :menutype')
            ->bind(':menutype', $menutype);

        $id = (int) $this->db->setQuery($query)->loadResult();

        if (!$id) {
            throw new \RuntimeException('Página inicial não encontrada no menu principal.');
        }

        $this->update('#__menu', $id, ['title' => 'Início', 'note' => $this->note('menu:inicio')]);
        $this->changed('Início (página inicial)');
    }

    private function ensureItem(string $key, array $item, ?string $label = null): int
    {
        $label ??= $item['title'];

        if ($id = $this->findByNote('#__menu', $key, ['client_id' => 0])) {
            $this->exists($label);

            return $id;
        }

        $id = $this->save($this->model('com_menus', 'Item'), $item + [
            'id'                => 0,
            'menutype'          => self::MAIN_MENU,
            'parent_id'         => 1,
            'note'              => $this->note($key),
            'published'         => 1,
            'access'            => 1,
            'language'          => '*',
            'client_id'         => 0,
            'browserNav'        => 0,
            'home'              => 0,
            'img'               => '',
            'component_id'      => 0,
            'template_style_id' => 0,
        ], $label);

        $this->created($label);

        return $id;
    }
}
