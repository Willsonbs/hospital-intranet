<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

\defined('_JEXEC') or die;

/**
 * Campos personalizados dos artigos (com_content), por tipo de conteúdo.
 * Cada grupo de campos vale para uma categoria raiz e todas as subcategorias.
 *
 * As listas (setores, públicos) são apenas o ponto de partida: o administrador
 * pode acrescentar opções em Conteúdo → Campos.
 */
final class FieldsStep extends AbstractStep
{
    private const CONTEXT = 'com_content.article';

    public const SETORES = [
        'enfermagem'         => 'Enfermagem',
        'corpo-clinico'      => 'Corpo Clínico',
        'farmacia'           => 'Farmácia',
        'ccih'               => 'Controle de Infecção (CCIH)',
        'seguranca-paciente' => 'Segurança do Paciente',
        'qualidade'          => 'Qualidade',
        'educacao'           => 'Educação Permanente',
        'centro-cirurgico'   => 'Centro Cirúrgico',
        'uti'                => 'UTI',
        'laboratorio'        => 'Laboratório',
        'nutricao'           => 'Nutrição',
        'rh'                 => 'Recursos Humanos',
        'ti'                 => 'Tecnologia da Informação',
        'financeiro'         => 'Financeiro',
        'faturamento'        => 'Faturamento',
        'diretoria'          => 'Diretoria',
    ];

    public const PUBLICOS = [
        'todos'          => 'Todos os colaboradores',
        'enfermagem'     => 'Enfermagem',
        'corpo-clinico'  => 'Corpo Clínico',
        'administrativo' => 'Administrativo',
        'gestao'         => 'Gestão',
        'ti'             => 'TI',
        'rh'             => 'RH',
        'apoio'          => 'Apoio e hotelaria',
    ];

    /** Ícones do sprite (scripts/icons.txt) oferecidos para os sistemas */
    public const ICONES = [
        'layout-grid'    => 'Grade (genérico)',
        'users'          => 'Pessoas',
        'user'           => 'Pessoa',
        'clipboard-list' => 'Prancheta / prontuário',
        'stethoscope'    => 'Estetoscópio',
        'hospital'       => 'Hospital',
        'heart-pulse'    => 'Coração / sinais vitais',
        'pill'           => 'Medicamento',
        'syringe'        => 'Seringa',
        'flask-conical'  => 'Laboratório',
        'activity'       => 'Monitoramento',
        'chart-column'   => 'Gráfico / indicadores',
        'calendar'       => 'Calendário',
        'monitor'        => 'Computador',
        'server'         => 'Servidor',
        'headset'        => 'Suporte',
        'file-text'      => 'Documento',
        'files'          => 'Documentos',
        'folder'         => 'Pasta',
        'book-open'      => 'Manual',
        'graduation-cap' => 'Treinamento',
        'briefcase'      => 'Administrativo',
        'wallet'         => 'Financeiro',
        'calculator'     => 'Calculadora',
        'landmark'       => 'Instituição',
        'package'        => 'Estoque',
        'truck'          => 'Logística',
        'mail'           => 'E-mail',
        'message-square' => 'Chat',
        'phone'          => 'Telefone / ramais',
        'shield-check'   => 'Segurança',
        'megaphone'      => 'Comunicação',
    ];

    public const CORES = [
        'teal'   => 'Verde-água',
        'blue'   => 'Azul',
        'green'  => 'Verde',
        'purple' => 'Roxo',
        'orange' => 'Laranja',
        'red'    => 'Vermelho',
        'slate'  => 'Cinza',
    ];

    public function title(): string
    {
        return 'Campos personalizados';
    }

    public function run(): void
    {
        $simNao = ['1' => 'Sim', '0' => 'Não'];

        $this->group('documento', 'Documento', 'biblioteca', [
            ['doc-codigo', 'Código', 'text', ['required' => 1, 'hint' => 'Ex.: POP-ENF-042']],
            ['doc-versao', 'Versão', 'text', ['required' => 1, 'default_value' => '1.0', 'hint' => 'Ex.: 4.2']],
            ['doc-status', 'Status', 'list', ['default_value' => 'vigente', 'options' => [
                'vigente' => 'Vigente', 'em-revisao' => 'Em revisão', 'obsoleto' => 'Obsoleto',
            ]]],
            ['doc-setor', 'Setor responsável', 'list', ['required' => 1, 'options' => self::SETORES, 'header' => 'Selecione o setor']],
            ['doc-revisao', 'Data de revisão', 'calendar', ['description' => 'Próxima revisão prevista.']],
            ['doc-publico', 'Público-alvo', 'checkboxes', ['options' => self::PUBLICOS]],
            ['doc-arquivo', 'Arquivo', 'document', ['description' => 'PDF ou documento do Office enviado pelo Gerenciador de Mídia.']],
        ], display: 2);

        $this->group('sistema', 'Sistema', 'sistemas', [
            ['sis-url', 'Endereço (URL)', 'url', ['required' => 1, 'hint' => 'https://', 'fieldparams' => ['schemes' => ['http', 'https'], 'relative' => 1]]],
            ['sis-icone', 'Ícone', 'list', ['default_value' => 'layout-grid', 'options' => self::ICONES]],
            ['sis-cor', 'Cor do ícone', 'list', ['default_value' => 'teal', 'options' => self::CORES]],
            ['sis-nova-aba', 'Abrir em nova aba', 'radio', ['default_value' => '1', 'options' => $simNao]],
            ['sis-acesso-rapido', 'Exibir no acesso rápido da página inicial', 'radio', ['default_value' => '0', 'options' => $simNao]],
        ], display: 0);

        $this->group('evento', 'Evento', 'eventos', [
            ['evt-inicio', 'Início', 'calendar', ['required' => 1, 'fieldparams' => ['showtime' => 1]]],
            ['evt-termino', 'Término', 'calendar', ['fieldparams' => ['showtime' => 1]]],
            ['evt-local', 'Local', 'text', ['hint' => 'Ex.: Auditório Principal']],
            ['evt-responsavel', 'Responsável', 'text', []],
            ['evt-link', 'Link (inscrição, transmissão)', 'url', ['fieldparams' => ['schemes' => ['http', 'https'], 'relative' => 1]]],
        ], display: 2);

        $this->group('aviso', 'Aviso', 'avisos', [
            ['avi-prioridade', 'Prioridade', 'list', ['default_value' => 'atencao', 'options' => [
                'informativo' => 'Informativo', 'atencao' => 'Atenção', 'critico' => 'Crítico',
            ]]],
            ['avi-publico', 'Público', 'checkboxes', ['options' => self::PUBLICOS]],
            ['avi-setor', 'Setor', 'list', ['options' => self::SETORES, 'header' => 'Todos os setores']],
        ], display: 0);
    }

    /**
     * @param  array  $fields   [nome, rótulo, tipo, opções]
     * @param  int    $display  Exibição automática no artigo: 0 = não (o template exibe), 2 = antes do texto
     */
    private function group(string $key, string $title, string $categoryKey, array $fields, int $display): void
    {
        $groupId = $this->findByNote('#__fields_groups', 'fieldgroup:' . $key, ['context' => self::CONTEXT]);

        if ($groupId) {
            $this->exists("Grupo \"$title\"");
        } else {
            $groupId = $this->save($this->model('com_fields', 'Group'), [
                'id'          => 0,
                'context'     => self::CONTEXT,
                'title'       => $title,
                'note'        => $this->note('fieldgroup:' . $key),
                'description' => '',
                'state'       => 1,
                'access'      => 1,
                'language'    => '*',
                'params'      => ['display_readonly' => '1'],
            ], "Grupo \"$title\"");
            $this->created("Grupo \"$title\"");
        }

        $categoryId = $this->categoryId($categoryKey);

        foreach ($fields as $ordering => [$name, $label, $type, $opts]) {
            $this->field($name, $label, $type, $opts, $groupId, $categoryId, $ordering + 1, $display);
        }
    }

    private function field(string $name, string $label, string $type, array $opts, int $groupId, int $categoryId, int $ordering, int $display): void
    {
        if ($this->findByNote('#__fields', 'field:' . $name, ['context' => self::CONTEXT])) {
            $this->exists("  $label");

            return;
        }

        $fieldparams = $opts['fieldparams'] ?? [];

        if (isset($opts['options'])) {
            $fieldparams['options'] = [];

            foreach (array_values(array_keys($opts['options'])) as $i => $value) {
                $fieldparams['options']['options' . $i] = ['name' => $opts['options'][$value], 'value' => (string) $value];
            }
        }

        if (isset($opts['header'])) {
            $fieldparams['header'] = $opts['header'];
        }

        $this->save($this->model('com_fields', 'Field'), [
            'id'               => 0,
            'context'          => self::CONTEXT,
            'group_id'         => $groupId,
            'assigned_cat_ids' => [$categoryId],
            'title'            => $label,
            'name'             => $name,
            'label'            => $label,
            'type'             => $type,
            'note'             => $this->note('field:' . $name),
            'description'      => $opts['description'] ?? '',
            'required'         => $opts['required'] ?? 0,
            'default_value'    => $opts['default_value'] ?? '',
            'ordering'         => $ordering,
            'state'            => 1,
            'access'           => 1,
            'language'         => '*',
            'fieldparams'      => $fieldparams,
            'params'           => [
                'hint'             => $opts['hint'] ?? '',
                'showlabel'        => '1',
                'display'          => (string) $display,
                'display_readonly' => '2',
            ],
        ], $label);

        $this->created("  $label");
    }
}
