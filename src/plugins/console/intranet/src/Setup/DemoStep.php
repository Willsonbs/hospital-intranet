<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;

\defined('_JEXEC') or die;

/**
 * Conteúdo de demonstração (§31 da especificação). Só roda com --demo.
 *
 * Todo item criado aqui tem a nota "intranet:demo:<chave>". As imagens são
 * ilustrações provisórias (scripts/build-demo-images.py).
 */
final class DemoStep extends AbstractStep
{
    private const IMAGES = ['certificacao', 'enfermagem', 'bem-estar', 'materiais'];

    public function title(): string
    {
        return 'Conteúdo de demonstração';
    }

    public function run(): void
    {
        // O ArticleModel carrega o formulário do artigo (FormBehaviorTrait::loadForm), que
        // usa JPATH_COMPONENT: constante definida só em requisições web, não no console.
        \defined('JPATH_COMPONENT') || \define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_content');

        $this->copyImages();
        $this->systems();
        $this->news();
        $this->documents();
        $this->alerts();
        $this->events();
        $this->ramais();
    }

    private function copyImages(): void
    {
        $target = JPATH_ROOT . '/images/demo';

        if (!is_dir($target)) {
            Folder::create($target);
        }

        foreach (self::IMAGES as $name) {
            $file = "$target/$name.webp";

            if (!is_file($file)) {
                copy(\dirname(__DIR__, 2) . "/demo/images/$name.webp", $file);
            }
        }
    }

    private function systems(): void
    {
        // [chave, subcategoria, título, descrição, url, ícone, cor, nova aba, acesso rápido]
        $systems = [
            ['portal', 'recursos-humanos', 'Portal do Colaborador', 'Holerites, benefícios e dados cadastrais', 'https://portal.santaaurora.example', 'users', 'blue', 1, 1],
            ['assistencial', 'assistenciais', 'Sistema Assistencial', 'Prontuário e segurança de pacientes', 'https://his.santaaurora.example', 'clipboard-list', 'green', 1, 1],
            ['indicadores', 'bi-e-indicadores', 'Indicadores', 'Painéis e resultados institucionais', 'https://bi.santaaurora.example', 'chart-column', 'purple', 1, 1],
            ['agenda', 'administrativos', 'Agenda de Salas', 'Reservas de espaços e equipamentos', 'https://agenda.santaaurora.example', 'calendar', 'orange', 1, 1],
            ['chamados', 'ti', 'Chamados de TI', 'Suporte técnico e acompanhamento', 'https://chamados.santaaurora.example', 'monitor', 'slate', 1, 1],
            ['documentos', 'administrativos', 'Documentos', 'Protocolos, manuais e formulários', '/documentos', 'file-text', 'red', 0, 1],
            ['prescricao', 'assistenciais', 'Prescrição Eletrônica', 'Prescrição e checagem de medicamentos', 'https://prescricao.santaaurora.example', 'pill', 'green', 1, 0],
            ['laboratorio', 'assistenciais', 'Resultados de Exames', 'Laudos laboratoriais e de imagem', 'https://lab.santaaurora.example', 'flask-conical', 'teal', 1, 0],
            ['email', 'ti', 'E-mail Institucional', 'Webmail e calendário corporativo', 'https://mail.santaaurora.example', 'mail', 'blue', 1, 0],
            ['erp', 'financeiro', 'ERP Financeiro', 'Contas, compras e orçamento', 'https://erp.santaaurora.example', 'wallet', 'orange', 1, 0],
            ['ead', 'recursos-humanos', 'Treinamentos EAD', 'Cursos obrigatórios e trilhas de capacitação', 'https://ead.santaaurora.example', 'graduation-cap', 'purple', 1, 0],
        ];

        foreach ($systems as $i => [$key, $cat, $title, $desc, $url, $icon, $tone, $newTab, $featured]) {
            $this->article('sistema/' . $key, [
                'catid'     => $this->categoryId('sistemas/' . $cat),
                'title'     => $title,
                'introtext' => "<p>$desc</p>",
                'featured'  => $featured,
                'ordering'  => $i + 1,
                'com_fields' => [
                    'sis-url'      => $url,
                    'sis-icone'    => $icon,
                    'sis-cor'      => $tone,
                    'sis-nova-aba' => (string) $newTab,
                ],
            ]);
        }

        $this->orderFeatured(array_map(static fn ($s) => 'sistema/' . $s[0], array_filter($systems, static fn ($s) => $s[8])));
    }

    private function news(): void
    {
        $news = [
            ['certificacao', 'institucional', 'Hospital recebe certificação de qualidade nacional', 2,
                'Reconhecimento destaca a segurança do paciente, a gestão de processos e o engajamento das equipes assistenciais e de apoio.',
                'certificacao'],
            ['enfermagem', 'pessoas', 'Semana da Enfermagem celebra quem transforma o cuidado', 6,
                'Programação inclui palestras, homenagens aos profissionais com mais tempo de casa e atividades de bem-estar em todos os turnos.',
                'enfermagem'],
            ['bem-estar', 'saude-e-bem-estar', 'Nova campanha incentiva hábitos saudáveis no trabalho', 10,
                'Ações de alimentação equilibrada, pausas ativas e acompanhamento nutricional estarão disponíveis para todos os colaboradores.',
                'bem-estar'],
            ['materiais', 'comunicados', 'Novo fluxo de solicitação de materiais começa na segunda-feira', 15,
                'Pedidos ao almoxarifado passam a ser feitos pelo sistema de chamados, com acompanhamento de status e prazos por setor.',
                'materiais'],
        ];

        foreach ($news as [$key, $cat, $title, $daysAgo, $intro, $image]) {
            $this->article('noticia/' . $key, [
                'catid'      => $this->categoryId('noticias/' . $cat),
                'title'      => $title,
                'introtext'  => "<p>$intro</p>",
                'fulltext'   => '<p>Texto completo da notícia de demonstração. Substitua por conteúdo real no painel.</p>',
                'publish_up' => $this->daysFromNow(-$daysAgo, '09:00'),
                'images'     => [
                    'image_intro'     => "images/demo/$image.webp#joomlaImage://local-images/demo/$image.webp?width=1200&height=750",
                    'image_intro_alt' => '',
                    'image_intro_alt_empty' => 1,
                ],
            ]);
        }
    }

    private function documents(): void
    {
        // [código, subcategoria, título, setor, versão, status, dias atrás, revisão em dias]
        $docs = [
            ['POP-ENF-042', 'pops', 'Administração segura de medicamentos', 'enfermagem', '4.2', 'vigente', 1, 300],
            ['PRT-CCIH-018', 'protocolos', 'Precauções e isolamento hospitalar', 'ccih', '3.0', 'vigente', 4, 240],
            ['POP-FAR-027', 'pops', 'Armazenamento de medicamentos termolábeis', 'farmacia', '2.1', 'em-revisao', 8, 20],
            ['PRT-SEG-005', 'protocolos', 'Identificação correta do paciente', 'seguranca-paciente', '5.0', 'vigente', 12, 330],
            ['MAN-TI-001', 'manuais', 'Manual do e-mail institucional', 'ti', '1.3', 'vigente', 30, 365],
            ['FOR-RH-010', 'formularios', 'Solicitação de férias', 'rh', '2.0', 'vigente', 45, 365],
        ];

        foreach ($docs as [$code, $cat, $title, $sector, $version, $status, $daysAgo, $reviewIn]) {
            $this->article('documento/' . strtolower($code), [
                'catid'      => $this->categoryId('biblioteca/' . $cat),
                'title'      => $title,
                'introtext'  => "<p>Documento institucional de demonstração: $title.</p>",
                'publish_up' => $this->daysFromNow(-$daysAgo, '08:00'),
                'com_fields' => [
                    'doc-codigo'  => $code,
                    'doc-versao'  => $version,
                    'doc-status'  => $status,
                    'doc-setor'   => $sector,
                    'doc-revisao' => $this->daysFromNow($reviewIn, '00:00'),
                    'doc-publico' => ['todos'],
                ],
            ]);
        }
    }

    private function alerts(): void
    {
        $this->article('aviso/manutencao', [
            'catid'        => $this->categoryId('avisos'),
            'title'        => 'Manutenção programada do sistema assistencial',
            'introtext'    => '<p>Hoje, das 22h às 23h. Durante a janela, use os formulários de contingência.</p>',
            'fulltext'     => '<p>A atualização melhora o desempenho do prontuário eletrônico. Em caso de dúvida, fale com a TI pelo ramal 2000.</p>',
            'publish_down' => $this->daysFromNow(2, '23:59'),
            'com_fields'   => ['avi-prioridade' => 'atencao', 'avi-publico' => ['todos']],
        ]);
    }

    private function events(): void
    {
        $events = [
            ['seguranca', 'Treinamento de Segurança do Paciente', 7, '14:00', '16:00', 'Auditório Principal', 'Educação Permanente'],
            ['vacinacao', 'Campanha de vacinação dos colaboradores', 14, '08:00', '17:00', 'Ambulatório', 'Medicina do Trabalho'],
            ['liderancas', 'Reunião geral de lideranças', 21, '10:00', '11:30', 'Sala de Reuniões 2', 'Diretoria'],
        ];

        foreach ($events as [$key, $title, $inDays, $start, $end, $place, $owner]) {
            $this->article('evento/' . $key, [
                'catid'      => $this->categoryId('eventos'),
                'title'      => $title,
                'introtext'  => "<p>$title. Evento de demonstração.</p>",
                'com_fields' => [
                    'evt-inicio'      => $this->daysFromNow($inDays, $start),
                    'evt-termino'     => $this->daysFromNow($inDays, $end),
                    'evt-local'       => $place,
                    'evt-responsavel' => $owner,
                ],
            ]);
        }
    }

    /** Ramais de exemplo (§13.1): só quando o diretório está vazio. */
    private function ramais(): void
    {
        $total = (int) $this->db->setQuery(
            $this->db->getQuery(true)->select('COUNT(*)')->from($this->db->quoteName('#__ramais'))
        )->loadResult();

        if ($total > 0) {
            $this->exists("Ramais ($total no diretório)");

            return;
        }

        $ramais = [
            ['Recepção', '2010', 'Térreo'],
            ['Pronto Atendimento', '2015 / 2016', 'Térreo'],
            ['Tecnologia da Informação', '2045', '1º andar'],
            ['Suporte de TI (plantão)', '2000', '1º andar'],
            ['Enfermagem', '2080', '2º andar'],
            ['Posto de Enfermagem — Ala A', '2081', '2º andar'],
            ['Posto de Enfermagem — Ala B', '2082', '2º andar'],
            ['Recursos Humanos', '2090', 'Prédio Administrativo'],
            ['Financeiro', '2100', 'Prédio Administrativo'],
            ['Faturamento', '2105', 'Prédio Administrativo'],
            ['Diretoria', '2110', 'Prédio Administrativo'],
            ['Farmácia', '2120', 'Térreo'],
            ['Almoxarifado', '2130', 'Subsolo'],
            ['Centro Cirúrgico', '2200', 'Centro Cirúrgico'],
            ['UTI Adulto', '2300', 'UTI'],
            ['UTI Neonatal', '2310', 'UTI'],
            ['Laboratório', '2400', 'Térreo'],
            ['Diagnóstico por Imagem', '2410', 'Térreo'],
            ['Ambulatório', '2500', 'Ambulatório'],
            ['Nutrição', '2600', 'Subsolo'],
            ['Controle de Infecção (CCIH)', '2610', '1º andar'],
            ['Qualidade e Segurança do Paciente', '2620', '1º andar'],
            ['Educação Permanente', '2630', '1º andar'],
            ['Manutenção', '2700', 'Subsolo'],
            ['Segurança Patrimonial', '2800', 'Térreo'],
        ];

        $now   = Factory::getDate()->toSql();
        $user  = (int) $this->app->getIdentity()->id;
        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__ramais'))
            ->columns($this->db->quoteName(['setor', 'ramal', 'localizacao', 'state', 'ordering', 'created', 'created_by', 'modified', 'modified_by']));

        foreach ($ramais as $i => [$setor, $ramal, $local]) {
            $query->values(implode(',', [
                $this->db->quote($setor), $this->db->quote($ramal), $this->db->quote($local),
                1, $i + 1, $this->db->quote($now), $user, $this->db->quote($now), $user,
            ]));
        }

        $this->db->setQuery($query)->execute();
        $this->created(\count($ramais) . ' ramais');
    }

    private function article(string $key, array $data): void
    {
        if ($this->findByNote('#__content', 'demo:' . $key)) {
            $this->exists($data['title']);

            return;
        }

        $this->save($this->model('com_content', 'Article'), $data + [
            'id'         => 0,
            'alias'      => '',
            'note'       => $this->note('demo:' . $key),
            'state'      => 1,
            'featured'   => 0,
            'access'     => 1,
            'language'   => '*',
            'fulltext'   => '',
            'publish_up' => $this->daysFromNow(-1, '08:00'),
            'images'     => [],
            'urls'       => [],
            'attribs'    => [],
            'metadata'   => [],
            'metadesc'   => '',
            'metakey'    => '',
            'com_fields' => [],
        ], $data['title']);

        $this->created($data['title']);
    }

    /** Ordem dos destaques (acesso rápido) = ordem da lista de chaves. */
    private function orderFeatured(array $keys): void
    {
        foreach (array_values($keys) as $position => $key) {
            $id = $this->findByNote('#__content', 'demo:' . $key);

            if (!$id) {
                continue;
            }

            $ordering = $position + 1;
            $this->db->setQuery(
                $this->db->getQuery(true)
                    ->update($this->db->quoteName('#__content_frontpage'))
                    ->set($this->db->quoteName('ordering') . ' = ' . $ordering)
                    ->where($this->db->quoteName('content_id') . ' = ' . $id)
            )->execute();
        }
    }

    /** Data/hora local (fuso do site) deslocada em dias, no formato aceito pelos formulários. */
    private function daysFromNow(int $days, string $time): string
    {
        $tz   = new \DateTimeZone($this->app->get('offset', 'UTC'));
        $date = (new \DateTimeImmutable('now', $tz))->modify("$days days")->format('Y-m-d');

        return Factory::getDate("$date $time:00", $tz)->toSql();
    }
}
