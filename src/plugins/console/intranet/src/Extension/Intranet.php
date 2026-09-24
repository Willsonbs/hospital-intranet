<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Extension;

use HospitalSantaAurora\Plugin\Console\Intranet\Command\AclReportCommand;
use HospitalSantaAurora\Plugin\Console\Intranet\Command\SetupCommand;
use Joomla\Application\ApplicationEvents;
use Joomla\Application\Event\ApplicationEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

\defined('_JEXEC') or die;

/**
 * Registra os comandos de console da intranet.
 */
final class Intranet extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ApplicationEvents::BEFORE_EXECUTE => 'registerCommands'];
    }

    public function registerCommands(ApplicationEvent $event): void
    {
        $event->getApplication()->addCommand(new SetupCommand());
        $event->getApplication()->addCommand(new AclReportCommand());
    }
}
