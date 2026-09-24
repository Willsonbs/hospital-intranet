<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Regras compartilhadas entre painel, site e módulo.
 */
abstract class DocumentosHelper
{
	public const STATUS = ['vigente', 'em_revisao', 'obsoleto'];

	/** Status exibidos no site por padrão (obsoletos só aparecem se filtrados) */
	public const STATUS_PUBLIC = ['vigente', 'em_revisao'];

	public static function statusLabel(string $status): string
	{
		return Text::_('COM_DOCUMENTOS_STATUS_' . strtoupper($status));
	}

	/**
	 * @return string[]
	 */
	public static function setores(): array
	{
		$raw = (string) ComponentHelper::getParams('com_documentos')->get('setores', '');

		return array_values(array_filter(array_map('trim', preg_split('/\R/', $raw))));
	}

	/**
	 * Revisão vencida: data de revisão anterior a hoje.
	 */
	public static function isRevisionOverdue(?string $date): bool
	{
		return $date && $date !== '0000-00-00' && $date < date('Y-m-d');
	}

	/**
	 * Caminho do arquivo vindo do campo de mídia ("files/documentos/x.pdf#joomlaImage://...").
	 */
	public static function filePath(string $value): string
	{
		return $value === '' ? '' : HTMLHelper::_('cleanImageURL', $value)->url;
	}

	public static function fileUrl(string $value): string
	{
		$path = self::filePath($value);

		if ($path === '' || preg_match('#^https?://#i', $path)) {
			return $path;
		}

		return Uri::root(true) . '/' . ltrim($path, '/');
	}

	/**
	 * Extensão e tamanho legível do arquivo local, para o botão de download.
	 *
	 * @return array{ext: string, size: string}
	 */
	public static function fileInfo(string $value): array
	{
		$path = self::filePath($value);
		$ext  = strtoupper(pathinfo(parse_url($path, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
		$full = JPATH_ROOT . '/' . ltrim($path, '/');
		$size = '';

		if ($path !== '' && is_file($full)) {
			$bytes = filesize($full);
			$size  = $bytes >= 1048576
				? number_format($bytes / 1048576, 1, ',', '.') . ' MB'
				: max(1, (int) round($bytes / 1024)) . ' KB';
		}

		return ['ext' => $ext, 'size' => $size];
	}

	public static function formatDate(?string $date): string
	{
		return ($date && $date !== '0000-00-00') ? HTMLHelper::_('date', $date, 'd/m/Y', null) : '';
	}
}
