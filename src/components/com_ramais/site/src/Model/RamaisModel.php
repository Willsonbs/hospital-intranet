<?php

/**
 * @package     HospitalSantaAurora.Component
 * @subpackage  com_ramais
 */

namespace HospitalSantaAurora\Component\Ramais\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Transliterate;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

\defined('_JEXEC') or die;

/**
 * Ramais ativos, com ordenação (?sort=setor|ramal|localizacao&dir=asc|desc).
 *
 * Sem paginação e sem filtrar no banco: a lista inteira vai para a página e a
 * busca (?q=) só marca quais linhas aparecem (ver matches()). Assim a busca em
 * tempo real no navegador pode mostrar de novo qualquer ramal ao apagar o texto.
 */
final class RamaisModel extends ListModel
{
    public const SORTABLE = ['setor', 'ramal', 'localizacao'];

    protected function populateState($ordering = null, $direction = null)
    {
        $app    = Factory::getApplication();
        $input  = $app->getInput();
        $params = $app->getParams();

        $this->setState('params', $params);
        $this->setState('filter.search', trim($input->getString('q', '')));

        // Ordem padrão do item de menu: por setor (A-Z) ou ordem personalizada do painel
        $default = $params->get('ordenacao', 'setor') === 'ordering' ? 'ordering' : 'setor';
        $sort    = $input->getCmd('sort', '');

        $this->setState('list.ordering', \in_array($sort, self::SORTABLE, true) ? $sort : $default);
        $this->setState('list.direction', strtolower($input->getCmd('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC');
        $this->setState('list.start', 0);
        $this->setState('list.limit', 0);
    }

    protected function getListQuery(): QueryInterface
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'setor', 'ramal', 'localizacao']))
            ->from($db->quoteName('#__ramais'))
            ->where($db->quoteName('state') . ' = 1');

        $dir = $this->getState('list.direction') === 'DESC' ? 'DESC' : 'ASC';

        match ($this->getState('list.ordering')) {
            'ramal'       => $query->order('CAST(' . $db->quoteName('ramal') . ' AS UNSIGNED) ' . $dir),
            'localizacao' => $query->order($db->quoteName('localizacao') . ' ' . $dir),
            'ordering'    => $query->order($db->quoteName('ordering') . ' ASC'),
            default       => $query->order($db->quoteName('setor') . ' ' . $dir),
        };

        return $query->order($db->quoteName('setor') . ' ASC');
    }

    /**
     * O ramal atende à busca? Todas as palavras precisam aparecer em alguma coluna,
     * sem diferenciar maiúsculas e acentos (mesma regra de directory.js).
     */
    public static function matches(object $item, string $search): bool
    {
        $haystack = self::normalize($item->setor . ' ' . $item->ramal . ' ' . $item->localizacao);

        foreach (preg_split('/\s+/u', self::normalize($search), -1, PREG_SPLIT_NO_EMPTY) as $term) {
            if (!str_contains($haystack, $term)) {
                return false;
            }
        }

        return true;
    }

    private static function normalize(string $text): string
    {
        return mb_strtolower(Transliterate::utf8_latin_to_ascii(trim($text)));
    }
}
