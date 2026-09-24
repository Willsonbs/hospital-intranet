<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_protocols
 */

defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

class ModHospitalProtocolsHelper
{
	/**
	 * Últimos documentos publicados (vigentes ou em revisão) das categorias escolhidas.
	 *
	 * @return  \stdClass[]
	 */
	public static function getItems(Registry $params): array
	{
		if (!class_exists(DocumentosHelper::class)) {
			return [];
		}

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$cats  = array_values(array_filter(array_map('intval', (array) $params->get('catids', []))));
		$limit = max(1, min(20, (int) $params->get('count', 4)));

		$query = $db->getQuery(true)
			->select('a.id, a.catid, a.codigo, a.titulo, a.setor, a.versao, a.status, a.arquivo, a.data_revisao')
			->select($db->quoteName('c.title', 'category_title'))
			->from($db->quoteName('#__hospital_documentos', 'a'))
			->join('INNER', $db->quoteName('#__categories', 'c'), 'c.id = a.catid AND c.published = 1')
			->where('a.state = 1')
			->whereIn('a.status', DocumentosHelper::STATUS_PUBLIC, ParameterType::STRING)
			->order('a.data_publicacao DESC, a.id DESC')
			->setLimit($limit);

		if ($cats) {
			$query->whereIn('a.catid', $cats);
		}

		return $db->setQuery($query)->loadObjectList();
	}
}
