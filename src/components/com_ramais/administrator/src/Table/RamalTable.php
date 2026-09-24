<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Event\DispatcherInterface;

\defined('_JEXEC') or die;

final class RamalTable extends Table
{
    protected $_supportNullValue = true;

    public function __construct(DatabaseInterface $db, ?DispatcherInterface $dispatcher = null)
    {
        $this->typeAlias = 'com_ramais.ramal';
        $this->setColumnAlias('published', 'state');

        parent::__construct('#__ramais', 'id', $db, $dispatcher);
    }

    public function check()
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        $this->setor       = trim((string) $this->setor);
        $this->ramal       = trim(preg_replace('/\s+/', ' ', (string) $this->ramal));
        $this->localizacao = trim((string) $this->localizacao);

        if ($this->setor === '') {
            $this->setError(Text::_('COM_RAMAIS_ERROR_SETOR_REQUIRED'));

            return false;
        }

        // Números, com separadores opcionais: "2010", "2010 / 2011", "2010-2012"
        if (!preg_match('/^\d{2,8}(\s*[\/,-]\s*\d{2,8})*$/', $this->ramal)) {
            $this->setError(Text::_('COM_RAMAIS_ERROR_RAMAL_INVALID'));

            return false;
        }

        return true;
    }

    public function store($updateNulls = true)
    {
        $date = Factory::getDate()->toSql();
        $user = Factory::getApplication()->getIdentity();

        if (!(int) $this->id) {
            $this->created    = $this->created ?: $date;
            $this->created_by = $this->created_by ?: (int) $user?->id;

            if (!(int) $this->ordering) {
                $this->ordering = $this->getNextOrder();
            }
        }

        $this->modified    = $date;
        $this->modified_by = (int) $user?->id;

        return parent::store($updateNulls);
    }
}
