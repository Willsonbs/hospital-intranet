<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  System.intranet
 */

\defined('_JEXEC') or die;

use HospitalSantaAurora\Plugin\System\Intranet\Extension\Intranet;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            static function (Container $container) {
                $plugin = new Intranet((array) PluginHelper::getPlugin('system', 'intranet'));
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
