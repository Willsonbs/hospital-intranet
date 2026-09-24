<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  System.intranet
 */

namespace HospitalSantaAurora\Plugin\System\Intranet\Extension;

use Joomla\CMS\Event\Application\AfterRouteEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\Event\SubscriberInterface;

\defined('_JEXEC') or die;

/**
 * Completa o ACL do Joomla no painel administrativo.
 *
 * No ACL nativo, "criar/editar" numa categoria vale tanto para os artigos quanto
 * para a própria categoria, e quem gerencia artigos também vê a lista de campos.
 * Os papéis Qualidade e Imprensa precisam publicar artigos sem poder renomear ou
 * criar categorias nem ver a estrutura dos campos. O Joomla já tira essas telas
 * do menu deles, mas elas continuam acessíveis pela URL: este plugin as bloqueia
 * para quem não é super usuário.
 */
final class Intranet extends CMSPlugin implements SubscriberInterface
{
    /** Componentes do painel restritos a super usuários */
    private const SUPERUSER_ONLY = ['com_categories', 'com_fields'];

    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return ['onAfterRoute' => 'blockRestrictedComponents'];
    }

    public function blockRestrictedComponents(AfterRouteEvent $event): void
    {
        $app = $this->getApplication();

        if (!$this->isRestrictedUser()) {
            return;
        }

        $option = $app->getInput()->getCmd('option');

        if (!\in_array($option, self::SUPERUSER_ONLY, true)) {
            return;
        }

        $app->enqueueMessage(Text::_('PLG_SYSTEM_INTRANET_BLOCKED'), 'warning');
        $app->redirect(Route::_('index.php', false), 303);
    }

    /** Usuário logado no painel que não é super usuário. */
    private function isRestrictedUser(): bool
    {
        $app = $this->getApplication();

        if (!$app->isClient('administrator')) {
            return false;
        }

        $user = $app->getIdentity();

        return $user !== null && $user->id > 0 && !$user->authorise('core.admin');
    }
}
