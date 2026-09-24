<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_quick_access
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class ModHospitalQuickAccessHelper
{
	private const TONES = ['blue', 'green', 'purple', 'orange', 'slate', 'red'];

	/**
	 * Atalhos publicados, visíveis para o nível de acesso atual e da categoria filtrada.
	 *
	 * @return  \stdClass[]
	 */
	public static function getItems(Registry $params): array
	{
		$levels   = Factory::getApplication()->getIdentity()->getAuthorisedViewLevels();
		$category = mb_strtolower(trim((string) $params->get('category_filter', '')));
		$items    = [];

		foreach ((array) $params->get('items', []) as $raw) {
			$item = (object) $raw;

			if (empty($item->published) || empty($item->title) || empty($item->url)) {
				continue;
			}

			if (!in_array((int) ($item->access ?? 1), $levels, true)) {
				continue;
			}

			if ($category !== '' && mb_strtolower(trim((string) ($item->category ?? ''))) !== $category) {
				continue;
			}

			$href = self::resolveUrl((string) $item->url);

			if ($href === null) {
				continue;
			}

			$items[] = (object) [
				'title'       => (string) $item->title,
				'description' => (string) ($item->description ?? ''),
				'href'        => $href,
				'icon'        => preg_replace('/[^a-z0-9\- ]/i', '', (string) ($item->icon ?? '')) ?: 'fa-solid fa-link',
				'tone'        => in_array($item->tone ?? '', self::TONES, true) ? $item->tone : 'green',
				'newTab'      => !empty($item->new_tab),
				'category'    => trim((string) ($item->category ?? '')),
			];
		}

		return $items;
	}

	/**
	 * Agrupa os atalhos por categoria, na ordem definida pelo administrador.
	 * Categorias fora da lista vão para o fim; categorias sem itens não aparecem.
	 *
	 * @return  array<string, \stdClass[]>
	 */
	public static function groupByCategory(array $items, string $order): array
	{
		$groups = [];

		foreach (preg_split('/\R/', $order) as $name) {
			if (($name = trim($name)) !== '') {
				$groups[mb_strtolower($name)] = ['title' => $name, 'items' => []];
			}
		}

		foreach ($items as $item) {
			$key = mb_strtolower($item->category ?: '—');
			$groups[$key] ??= ['title' => $item->category ?: '—', 'items' => []];
			$groups[$key]['items'][] = $item;
		}

		return array_filter($groups, static fn ($group) => $group['items']);
	}

	/**
	 * Aceita http(s), caminhos relativos e links internos "index.php?...". Recusa outros esquemas.
	 */
	private static function resolveUrl(string $url): ?string
	{
		$url = trim($url);

		if (str_starts_with($url, 'index.php')) {
			return Route::_($url);
		}

		$scheme = parse_url($url, PHP_URL_SCHEME);

		if ($scheme !== null && !in_array(strtolower($scheme), ['http', 'https'], true)) {
			return null;
		}

		return $url;
	}
}
