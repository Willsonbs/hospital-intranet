<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

\defined('_JEXEC') or die;

/**
 * Árvore de categorias de conteúdo (com_content). Ver PLANO.md, seção 2.
 */
final class CategoriesStep extends AbstractStep
{
    /** alias => [título, [alias => título dos filhos]] */
    public const TREE = [
        'noticias' => ['Notícias', [
            'institucional'     => 'Institucional',
            'pessoas'           => 'Pessoas',
            'saude-e-bem-estar' => 'Saúde e Bem-estar',
            'tecnologia'        => 'Tecnologia',
            'recursos-humanos'  => 'Recursos Humanos',
            'eventos'           => 'Eventos',
            'comunicados'       => 'Comunicados',
            'ti'                => 'TI',
        ]],
        'biblioteca' => ['Biblioteca', [
            'protocolos'   => 'Protocolos',
            'pops'         => 'POPs',
            'manuais'      => 'Manuais',
            'formularios'  => 'Formulários',
            'politicas'    => 'Políticas',
            'normas'       => 'Normas',
            'fluxogramas'  => 'Fluxogramas',
            'treinamentos' => 'Treinamentos',
            'rh'           => 'Documentos de RH',
            'ti'           => 'Documentos de TI',
        ]],
        'sistemas' => ['Sistemas', [
            'assistenciais'    => 'Assistenciais',
            'administrativos'  => 'Administrativos',
            'recursos-humanos' => 'Recursos Humanos',
            'financeiro'       => 'Financeiro',
            'ti'               => 'TI',
            'bi-e-indicadores' => 'BI e Indicadores',
        ]],
        'eventos' => ['Eventos', []],
        'avisos'  => ['Avisos', []],
    ];

    public function title(): string
    {
        return 'Categorias';
    }

    public function run(): void
    {
        foreach (self::TREE as $alias => [$title, $children]) {
            $parentId = $this->ensure($alias, $title, $alias, 1);

            foreach ($children as $childAlias => $childTitle) {
                $this->ensure("$alias/$childAlias", "$title › $childTitle", $childAlias, $parentId, $childTitle);
            }
        }
    }

    private function ensure(string $key, string $label, string $alias, int $parentId, ?string $title = null): int
    {
        if ($id = $this->findByNote('#__categories', 'cat:' . $key, ['extension' => 'com_content'])) {
            $this->exists($label);

            return $id;
        }

        $id = $this->save($this->model('com_categories', 'Category'), [
            'id'          => 0,
            'parent_id'   => $parentId,
            'extension'   => 'com_content',
            'title'       => $title ?? $label,
            'alias'       => $alias,
            'note'        => $this->note('cat:' . $key),
            'published'   => 1,
            'access'      => 1,
            'language'    => '*',
            'description' => '',
            'params'      => [],
            'metadata'    => [],
        ], $label);

        $this->created($label);

        return $id;
    }
}
