<?php

/**
 * Funções de apoio usadas pelos overrides do template.
 * Inclua com: require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Transliterate;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

if (!class_exists('HospitalIntranetTemplate')) {
    final class HospitalIntranetTemplate
    {
        /** Tons aceitos pelo CSS (.tone-*) */
        private const TONES = ['teal', 'blue', 'green', 'purple', 'orange', 'red', 'slate'];

        /** @var array<int, array<string, object>> */
        private static array $fields = [];

        /** Campos personalizados do artigo, indexados pelo nome (ex.: "doc-codigo"). */
        public static function fields(object $item): array
        {
            $id = (int) $item->id;

            if (!isset(self::$fields[$id])) {
                $list = FieldsHelper::getFields('com_content.article', $item, true);
                self::$fields[$id] = array_column($list, null, 'name');
            }

            return self::$fields[$id];
        }

        /**
         * Valor gravado do campo: para listas e rádios, o "value" da opção escolhida;
         * para checkboxes, um array de values.
         */
        public static function raw(object $item, string $name, mixed $default = ''): mixed
        {
            $field = self::fields($item)[$name] ?? null;
            $value = $field?->rawvalue;

            // O Joomla entrega listas de escolha única como array de um item
            if (\is_array($value) && $field->type !== 'checkboxes' && \count($value) <= 1) {
                $value = $value ? reset($value) : null;
            }

            return ($value === null || $value === '' || $value === []) ? $default : $value;
        }

        /** Valor exibível do campo, como texto (para listas, o rótulo da opção). */
        public static function text(object $item, string $name): string
        {
            $field = self::fields($item)[$name] ?? null;

            return $field ? trim(html_entity_decode(strip_tags((string) $field->value), ENT_QUOTES, 'UTF-8')) : '';
        }

        public static function tone(string $tone): string
        {
            return 'tone-' . (\in_array($tone, self::TONES, true) ? $tone : 'teal');
        }

        public static function icon(string $name, string $class = '', string $label = ''): string
        {
            return LayoutHelper::render('hospital.icon', ['name' => $name, 'class' => $class, 'label' => $label]);
        }

        /** Texto puro e curto a partir do HTML da introdução. */
        public static function excerpt(string $html, int $limit = 160): string
        {
            $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8')));

            return $limit > 0 ? HTMLHelper::_('string.truncate', $text, $limit, true, false) : $text;
        }

        /** Imagem de introdução do artigo: ['url', 'alt', 'width', 'height'] ou null. */
        public static function introImage(object $item): ?array
        {
            $images = json_decode($item->images ?? '{}');

            if (empty($images->image_intro)) {
                return null;
            }

            $clean = HTMLHelper::_('cleanImageURL', $images->image_intro);

            return [
                'url'    => $clean->url,
                'alt'    => empty($images->image_intro_alt_empty) ? (string) ($images->image_intro_alt ?? '') : '',
                'width'  => (int) ($clean->attributes['width'] ?? 0),
                'height' => (int) ($clean->attributes['height'] ?? 0),
            ];
        }

        /**
         * Seção da intranet a que a categoria pertence ("noticias", "biblioteca", "sistemas",
         * "eventos", "avisos"), pela nota que o intranet:setup grava na categoria raiz.
         * Não depende do alias: renomear a categoria no painel não muda a seção.
         */
        public static function section(int $catid): string
        {
            static $cache = [];

            if (!isset($cache[$catid])) {
                $node = Factory::getApplication()->bootComponent('com_content')->getCategory()->get($catid);

                while ($node && (int) $node->level > 1) {
                    $node = $node->getParent();
                }

                $note = (string) ($node->note ?? '');
                $cache[$catid] = str_starts_with($note, 'intranet:cat:') ? substr($note, 13) : (string) ($node->alias ?? '');
            }

            return $cache[$catid];
        }

        /** Nó da categoria (com_content). */
        public static function category(int $catid): ?object
        {
            return Factory::getApplication()->bootComponent('com_content')->getCategory()->get($catid) ?: null;
        }

        /** A categoria $catid é uma das $allowed ou está dentro de uma delas? */
        public static function inCategories(int $catid, array $allowed): bool
        {
            for ($node = self::category($catid); $node && (int) $node->level > 0; $node = $node->getParent()) {
                if (\in_array((int) $node->id, $allowed, true)) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Categorias que o item de menu mostra, pela opção "Mostrar só estas subcategorias"
         * (plugin Sistema - Intranet). Vazio = todas.
         */
        public static function menuCategories(object $params): array
        {
            return array_values(array_filter(array_map('intval', (array) $params->get('intranet_categorias', []))));
        }

        /**
         * Artigos de uma página de categoria (blog), filtrados pelas categorias do item de menu.
         * Uso no template: T::categoryItems(array_merge($this->lead_items, $this->intro_items, $this->link_items), $this->params)
         */
        public static function categoryItems(array $items, object $params): array
        {
            $allowed = self::menuCategories($params);

            return $allowed ? array_values(array_filter($items, static fn ($i) => self::inCategories((int) $i->catid, $allowed))) : $items;
        }

        /**
         * Subcategorias para filtros/abas: as escolhidas no item de menu ou, se nenhuma,
         * as filhas da categoria raiz da página.
         */
        public static function filterCategories(object $category, object $params): array
        {
            if ($allowed = self::menuCategories($params)) {
                return array_values(array_filter(array_map([self::class, 'category'], $allowed)));
            }

            $root = $category;

            while ($root && (int) $root->level > 1) {
                $root = $root->getParent();
            }

            return $root ? $root->getChildren() : [];
        }

        /** Texto para comparação: minúsculas, sem acentos (mesma regra do list-filter.js). */
        public static function normalize(string $text): string
        {
            return mb_strtolower(Transliterate::utf8_latin_to_ascii(trim(preg_replace('/\s+/u', ' ', $text))));
        }

        /** Todas as palavras da busca aparecem no texto? */
        public static function matches(string $haystack, string $search): bool
        {
            foreach (preg_split('/\s+/u', self::normalize($search), -1, PREG_SPLIT_NO_EMPTY) as $term) {
                if (!str_contains($haystack, $term)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Arquivo de um campo do tipo "document": ['url', 'ext', 'size'] ou null.
         * O tamanho só é informado para arquivos do próprio servidor.
         */
        public static function file(object $item, string $name): ?array
        {
            $value = self::fields($item)[$name]->rawvalue ?? null;
            $value = \is_string($value) ? json_decode($value, true) : $value;
            $path  = \is_array($value) ? (string) ($value['file'] ?? '') : '';

            if ($path === '') {
                return null;
            }

            $path    = MediaHelper::getCleanMediaFieldValue($path);
            $isLocal = !preg_match('#^[a-z][a-z0-9+.-]*://#i', $path);
            $full    = JPATH_ROOT . '/' . ltrim($path, '/');

            if ($isLocal && !is_file($full)) {
                return null;
            }

            return [
                'url'  => $isLocal ? Uri::root(true) . '/' . ltrim($path, '/') : $path,
                'ext'  => strtoupper(pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_EXTENSION)),
                'size' => $isLocal ? self::bytes((int) filesize($full)) : '',
            ];
        }

        /** Tamanho legível: "1 KB", "250 KB", "2,4 MB" (nunca em bytes). */
        public static function bytes(int $bytes): string
        {
            if ($bytes < 1024 * 1024) {
                return max(1, (int) ceil($bytes / 1024)) . ' KB';
            }

            return number_format($bytes / 1024 / 1024, 1, ',', '.') . ' MB';
        }

        /** Data de um campo calendário (gravado em UTC) no fuso do site. */
        public static function date(object $item, string $name, string $format = 'd/m/Y'): string
        {
            $value = (string) self::raw($item, $name);

            return $value === '' ? '' : HTMLHelper::_('date', $value, $format);
        }

        /** Estilo do aviso pela prioridade: ['key', 'class', 'icon', 'label', 'rank']. */
        public static function alertStyle(object $item): array
        {
            $styles = [
                'critico'     => ['alert--danger', 'circle-x', 'TPL_HOSPITAL_INTRANET_ALERT_CRITICAL'],
                'atencao'     => ['alert--warning', 'triangle-alert', 'TPL_HOSPITAL_INTRANET_ALERT_IMPORTANT'],
                'informativo' => ['', 'info', 'TPL_HOSPITAL_INTRANET_ALERT_INFO'],
            ];
            $key = (string) self::raw($item, 'avi-prioridade', 'atencao');
            $key = isset($styles[$key]) ? $key : 'atencao';

            return [
                'key'   => $key,
                'class' => $styles[$key][0],
                'icon'  => $styles[$key][1],
                'label' => $styles[$key][2],
                'rank'  => array_search($key, array_keys($styles), true),
            ];
        }

        /** Avisos do mais urgente para o menos urgente (mantém a ordem original no empate). */
        public static function sortAlerts(array $items): array
        {
            $items = array_values($items);
            usort($items, static fn ($a, $b) => self::alertStyle($a)['rank'] <=> self::alertStyle($b)['rank']);

            return $items;
        }

        public static function e(?string $value): string
        {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }
    }
}
