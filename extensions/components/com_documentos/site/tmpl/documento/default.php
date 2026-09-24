<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

defined('_JEXEC') or die;

use Hospital\Component\Documentos\Administrator\Helper\DocumentosHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Hospital\Component\Documentos\Site\View\Documento\HtmlView $this */

$item    = $this->item;
$file    = DocumentosHelper::fileInfo((string) $item->arquivo);
$fileUrl = DocumentosHelper::fileUrl((string) $item->arquivo);
$overdue = $item->status !== 'obsoleto' && DocumentosHelper::isRevisionOverdue($item->data_revisao);
$active  = Factory::getApplication()->getMenu()->getActive();
$back    = $active ? Route::_('index.php?Itemid=' . (int) $active->id) : Route::_('index.php?option=com_documentos&view=documentos');
$meta    = [
	'COM_DOCUMENTOS_FIELD_SETOR'           => $item->setor,
	'COM_DOCUMENTOS_FIELD_CATEGORIA'       => $item->category_title,
	'COM_DOCUMENTOS_FIELD_VERSAO'          => $item->versao,
	'COM_DOCUMENTOS_FIELD_DATA_PUBLICACAO' => DocumentosHelper::formatDate($item->data_publicacao),
	'COM_DOCUMENTOS_FIELD_DATA_REVISAO'    => DocumentosHelper::formatDate($item->data_revisao),
	'COM_DOCUMENTOS_FIELD_AUTOR'           => $item->autor,
	'COM_DOCUMENTOS_FIELD_PUBLICO_ALVO'    => $item->publico_alvo,
];
?>
<article class="hi-card hi-document" aria-labelledby="doc-title">
	<a class="hi-back" href="<?php echo $this->escape($back); ?>">
		<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> <?php echo Text::_('COM_DOCUMENTOS_BACK'); ?>
	</a>

	<header class="hi-document__head">
		<p class="hi-doc__code"><?php echo $this->escape($item->codigo); ?></p>
		<h1 id="doc-title" class="hi-document__title"><?php echo $this->escape($item->titulo); ?></h1>
		<p class="hi-document__badges">
			<span class="hi-badge hi-badge--<?php echo $this->escape($item->status); ?>"><?php echo DocumentosHelper::statusLabel($item->status); ?></span>
			<?php if ($overdue) : ?>
				<span class="hi-badge hi-badge--danger"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> <?php echo Text::_('COM_DOCUMENTOS_REVISION_OVERDUE'); ?></span>
			<?php endif; ?>
		</p>
	</header>

	<?php if ($item->status === 'obsoleto') : ?>
		<p class="hi-notice hi-notice--warning" role="note">
			<i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i> <?php echo Text::_('COM_DOCUMENTOS_OBSOLETE_NOTICE'); ?>
		</p>
	<?php endif; ?>

	<?php if (trim((string) $item->descricao) !== '') : ?>
		<div class="hi-document__desc"><?php echo nl2br($this->escape($item->descricao)); ?></div>
	<?php endif; ?>

	<dl class="hi-meta">
		<?php foreach ($meta as $label => $value) : ?>
			<?php if ((string) $value !== '') : ?>
				<div>
					<dt><?php echo Text::_($label); ?></dt>
					<dd><?php echo $this->escape($value); ?></dd>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</dl>

	<?php if ($fileUrl !== '') : ?>
		<div class="hi-document__actions">
			<a class="hi-btn" href="<?php echo $this->escape($fileUrl); ?>" download>
				<i class="fa-solid fa-download" aria-hidden="true"></i>
				<?php echo Text::sprintf('COM_DOCUMENTOS_DOWNLOAD', $file['ext']); ?>
				<?php if ($file['size']) : ?><span class="hi-btn__hint">(<?php echo $file['size']; ?>)</span><?php endif; ?>
			</a>
			<?php if ($file['ext'] === 'PDF') : ?>
				<a class="hi-btn hi-btn--ghost" href="<?php echo $this->escape($fileUrl); ?>" target="_blank" rel="noopener">
					<i class="fa-regular fa-eye" aria-hidden="true"></i> <?php echo Text::_('COM_DOCUMENTOS_VIEW'); ?>
					<span class="visually-hidden"><?php echo Text::_('COM_DOCUMENTOS_OPENS_NEW_TAB'); ?></span>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</article>
