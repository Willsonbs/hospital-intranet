<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 *
 * Item da ProtocolList: ícone, código, título, setor/categoria, versão e situação.
 * Usado na página da biblioteca e no módulo "Últimos protocolos adicionados".
 *
 * @var array $displayData ['item' => object, 'link' => string, 'showCategory' => bool]
 */

defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Language\Text;

$item         = $displayData['item'];
$showCategory = $displayData['showCategory'] ?? true;
$overdue      = $item->status !== 'obsoleto' && DocumentosHelper::isRevisionOverdue($item->data_revisao ?? null);
$icon         = [
	'PDF' => 'fa-file-pdf', 'DOC' => 'fa-file-word', 'DOCX' => 'fa-file-word', 'ODT' => 'fa-file-word',
	'XLS' => 'fa-file-excel', 'XLSX' => 'fa-file-excel', 'ODS' => 'fa-file-excel', 'CSV' => 'fa-file-csv',
	'PPT' => 'fa-file-powerpoint', 'PPTX' => 'fa-file-powerpoint', 'ODP' => 'fa-file-powerpoint',
][strtoupper(pathinfo(DocumentosHelper::filePath((string) $item->arquivo), PATHINFO_EXTENSION))] ?? 'fa-file-lines';

$esc = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<a class="hi-doc" href="<?php echo $esc($displayData['link']); ?>">
	<span class="hi-doc__icon" aria-hidden="true"><i class="fa-regular <?php echo $icon; ?>"></i></span>
	<span class="hi-doc__body">
		<span class="hi-doc__code"><?php echo $esc($item->codigo); ?></span>
		<span class="hi-doc__title"><?php echo $esc($item->titulo); ?></span>
		<span class="hi-doc__meta">
			<span><?php echo $esc($item->setor); ?></span>
			<?php if ($showCategory && !empty($item->category_title)) : ?>
				<span class="hi-doc__sep" aria-hidden="true">·</span>
				<span><?php echo $esc($item->category_title); ?></span>
			<?php endif; ?>
		</span>
	</span>
	<span class="hi-doc__aside">
		<span class="hi-doc__version"><?php echo Text::sprintf('COM_DOCUMENTOS_VERSION_N', $esc($item->versao)); ?></span>
		<?php if ($item->status !== 'vigente') : ?>
			<span class="hi-badge hi-badge--<?php echo $esc($item->status); ?>"><?php echo DocumentosHelper::statusLabel($item->status); ?></span>
		<?php endif; ?>
		<?php if ($overdue) : ?>
			<span class="hi-badge hi-badge--danger"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?php echo Text::_('COM_DOCUMENTOS_REVISION_OVERDUE'); ?></span>
		<?php endif; ?>
	</span>
	<i class="fa-solid fa-chevron-right hi-doc__chevron" aria-hidden="true"></i>
</a>
