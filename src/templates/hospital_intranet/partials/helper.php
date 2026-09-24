<?php

/**
 * Funções de apoio usadas pelos overrides do template.
 * Inclua com: require_once JPATH_THEMES . '/hospital_intranet/partials/helper.php';
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
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

        public static function e(?string $value): string
        {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        }
    }
}
