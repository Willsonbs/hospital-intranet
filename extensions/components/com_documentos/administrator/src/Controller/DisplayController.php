<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_documentos
 */

namespace Hospital\Component\Documentos\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class DisplayController extends BaseController
{
	protected $default_view = 'documentos';

	public function display($cachable = false, $urlparams = [])
	{
		$view   = $this->input->get('view', $this->default_view);
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Impede abrir o formulário de edição direto pela URL sem passar pelo controller
		if ($view === 'documento' && $layout === 'edit' && !$this->checkEditId('com_documentos.edit.documento', $id)) {
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_documentos&view=documentos', false));

			return false;
		}

		return parent::display($cachable, $urlparams);
	}
}
