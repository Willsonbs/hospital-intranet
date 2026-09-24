<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  mod_hospital_protocols
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \stdClass[] $items */

// Rótulos compartilhados com o componente (status, versão)
Factory::getApplication()->getLanguage()->load('com_documentos', JPATH_SITE . '/components/com_documentos');

$layouts = JPATH_SITE . '/components/com_documentos/layouts';
?>
<ul class="hi-doc-list hi-doc-list--card">
	<?php foreach ($items as $item) : ?>
		<li>
			<?php echo LayoutHelper::render('documento-row', [
				'item'         => $item,
				'link'         => Route::_('index.php?option=com_documentos&view=documento&id=' . (int) $item->id),
				'showCategory' => false,
			], $layouts); ?>
		</li>
	<?php endforeach; ?>
</ul>
