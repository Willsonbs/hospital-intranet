<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Site\View\Ramais;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

\defined('_JEXEC') or die;

final class HtmlView extends BaseHtmlView
{
    protected array $items = [];

    protected $state;

    protected $params;

    public function display($tpl = null): void
    {
        $model = $this->getModel();

        $this->items  = $model->getItems() ?: [];
        $this->state  = $model->getState();
        $this->params = $this->state->get('params');

        $app    = Factory::getApplication();
        $active = $app->getMenu()->getActive();
        $title  = $this->params->get('page_title', $active?->title ?: Text::_('COM_RAMAIS_DIRECTORY'));

        $this->setDocumentTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->getDocument()->setDescription($this->params->get('menu-meta_description'));
        }

        $this->getDocument()->getWebAssetManager()
            ->getRegistry()->addExtensionRegistryFile('com_ramais');
        $this->getDocument()->getWebAssetManager()->useScript('com_ramais.directory');

        parent::display($tpl);
    }
}
