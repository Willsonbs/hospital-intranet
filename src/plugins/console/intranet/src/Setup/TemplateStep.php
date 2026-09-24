<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

\defined('_JEXEC') or die;

/**
 * Define o template hospital_intranet como padrão do site.
 */
final class TemplateStep extends AbstractStep
{
    public function title(): string
    {
        return 'Template';
    }

    public function run(): void
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(['id', 'home']))
            ->from($this->db->quoteName('#__template_styles'))
            ->where($this->db->quoteName('template') . ' = ' . $this->db->quote('hospital_intranet'))
            ->where($this->db->quoteName('client_id') . ' = 0')
            ->order($this->db->quoteName('id'));

        $style = $this->db->setQuery($query, 0, 1)->loadObject()
            ?? throw new \RuntimeException('Template hospital_intranet não está instalado.');

        if ($style->home === '1') {
            $this->exists('Template padrão: hospital_intranet');

            return;
        }

        $this->db->setQuery(
            $this->db->getQuery(true)
                ->update($this->db->quoteName('#__template_styles'))
                ->set($this->db->quoteName('home') . ' = ' . $this->db->quote('0'))
                ->where($this->db->quoteName('client_id') . ' = 0')
        )->execute();

        $this->update('#__template_styles', (int) $style->id, [
            'home'  => '1',
            'title' => 'Hospital Intranet - Padrão',
        ]);

        $this->changed('Template padrão: hospital_intranet');
    }
}
