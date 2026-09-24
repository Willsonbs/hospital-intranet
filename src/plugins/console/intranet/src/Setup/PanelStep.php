<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

\defined('_JEXEC') or die;

/**
 * Painel administrativo mais limpo para os papéis da intranet: sem o tour de
 * boas-vindas automático e sem o pedido de envio de estatísticas ao joomla.org
 * (a intranet roda em rede interna e não deve enviar dados para fora).
 */
final class PanelStep extends AbstractStep
{
    public function title(): string
    {
        return 'Painel administrativo';
    }

    public function run(): void
    {
        $this->db->setQuery(
            $this->db->getQuery(true)
                ->update($this->db->quoteName('#__guidedtours'))
                ->set($this->db->quoteName('autostart') . ' = 0')
                ->where($this->db->quoteName('autostart') . ' = 1')
        )->execute();

        $this->db->getAffectedRows() > 0
            ? $this->changed('Tour de boas-vindas não abre mais sozinho (continua em Ajuda)')
            : $this->exists('Tour de boas-vindas automático desativado');

        $this->db->setQuery(
            $this->db->getQuery(true)
                ->update($this->db->quoteName('#__extensions'))
                ->set($this->db->quoteName('enabled') . ' = 0')
                ->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
                ->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('system'))
                ->where($this->db->quoteName('element') . ' = ' . $this->db->quote('stats'))
                ->where($this->db->quoteName('enabled') . ' = 1')
        )->execute();

        $this->db->getAffectedRows() > 0
            ? $this->changed('Coleta de estatísticas do Joomla desativada')
            : $this->exists('Coleta de estatísticas do Joomla desativada');
    }
}
