<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

final class DisplayController extends BaseController
{
    protected $default_view = 'ramais';

    public function display($cachable = false, $urlparams = [])
    {
        $view   = $this->input->get('view', 'ramais');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        // O formulário só abre pelo fluxo normal (Novo / Editar), não por URL direta
        if ($view === 'ramal' && $layout === 'edit' && !$this->checkEditId('com_ramais.edit.ramal', $id)) {
            if (!\count($this->app->getMessageQueue())) {
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            }

            $this->setRedirect(Route::_('index.php?option=com_ramais&view=ramais', false));

            return false;
        }

        return parent::display();
    }
}
