<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

defined('_JEXEC') or die;

use Hospital\Component\Ramais\Administrator\Extension\RamaisComponent;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new MVCFactory('\\Hospital\\Component\\Ramais'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Hospital\\Component\\Ramais'));
		$container->registerServiceProvider(new RouterFactory('\\Hospital\\Component\\Ramais'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new RamaisComponent($container->get(ComponentDispatcherFactoryInterface::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};
