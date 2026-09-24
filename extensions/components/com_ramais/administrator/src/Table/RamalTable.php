<?php

/**
 * @package     Intranet Hospitalar
 * @subpackage  com_ramais
 */

namespace Hospital\Component\Ramais\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class RamalTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__hospital_ramais', 'id', $db);
		$this->setColumnAlias('published', 'state');
	}

	public function check()
	{
		$this->setor       = trim((string) $this->setor);
		$this->ramal       = trim((string) $this->ramal);
		$this->localizacao = trim((string) $this->localizacao);

		if ($this->setor === '' || $this->ramal === '') {
			$this->setError(Text::_('COM_RAMAIS_ERROR_REQUIRED'));

			return false;
		}

		if (!preg_match('#^[0-9 ()+\-/]+$#', $this->ramal)) {
			$this->setError(Text::_('COM_RAMAIS_ERROR_RAMAL_FORMAT'));

			return false;
		}

		return parent::check();
	}

	public function store($updateNulls = true)
	{
		$now    = Factory::getDate()->toSql();
		$userId = (int) Factory::getApplication()->getIdentity()->id;

		if (!$this->id) {
			$this->created    = $now;
			$this->created_by = $userId;
		}

		$this->modified    = $now;
		$this->modified_by = $userId;

		return parent::store($updateNulls);
	}
}
