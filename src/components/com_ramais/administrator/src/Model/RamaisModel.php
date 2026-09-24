<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

\defined('_JEXEC') or die;

/** Lista de ramais no painel (todos os status). */
final class RamaisModel extends ListModel
{
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        $config['filter_fields'] ??= [
            'id', 'a.id',
            'setor', 'a.setor',
            'ramal', 'a.ramal',
            'localizacao', 'a.localizacao',
            'state', 'a.state',
            'ordering', 'a.ordering',
        ];

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'a.setor', $direction = 'asc')
    {
        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName([
                'a.id', 'a.setor', 'a.ramal', 'a.localizacao', 'a.state', 'a.ordering',
                'a.checked_out', 'a.checked_out_time',
            ]))
            ->select($db->quoteName('uc.name', 'editor'))
            ->from($db->quoteName('#__ramais', 'a'))
            ->join('LEFT', $db->quoteName('#__users', 'uc'), $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'));

        $state = (string) $this->getState('filter.state');

        if (is_numeric($state)) {
            $state = (int) $state;
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, ParameterType::INTEGER);
        } elseif ($state === '') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        if ($search = trim((string) $this->getState('filter.search'))) {
            if (stripos($search, 'id:') === 0) {
                $id = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :id')->bind(':id', $id, ParameterType::INTEGER);
            } else {
                $like = '%' . str_replace(' ', '%', $search) . '%';
                $query->where('(' . implode(' OR ', [
                    $db->quoteName('a.setor') . ' LIKE :s1',
                    $db->quoteName('a.ramal') . ' LIKE :s2',
                    $db->quoteName('a.localizacao') . ' LIKE :s3',
                ]) . ')')
                    ->bind(':s1', $like)
                    ->bind(':s2', $like)
                    ->bind(':s3', $like);
            }
        }

        $ordering  = $this->getState('list.ordering', 'a.setor');
        $direction = strtoupper($this->getState('list.direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        if ($ordering === 'a.ramal') {
            // Ordem numérica ("900" antes de "2010")
            $query->order('CAST(' . $db->quoteName('a.ramal') . ' AS UNSIGNED) ' . $direction);
        } else {
            $query->order($db->quoteName($db->escape($ordering)) . ' ' . $direction);
        }

        return $query->order($db->quoteName('a.setor') . ' ASC');
    }
}
