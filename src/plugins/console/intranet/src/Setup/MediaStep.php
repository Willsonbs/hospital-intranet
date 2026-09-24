<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\Filesystem\Folder;

\defined('_JEXEC') or die;

/**
 * Pastas do Gerenciador de Mídia usadas pelos papéis: arquivos da Biblioteca e
 * imagens de notícias/eventos.
 */
final class MediaStep extends AbstractStep
{
    public const FOLDERS = [
        'documentos' => 'Arquivos da Biblioteca (protocolos, POPs, manuais…)',
        'noticias'   => 'Imagens de notícias',
        'eventos'    => 'Imagens de eventos',
    ];

    public function title(): string
    {
        return 'Pastas de mídia';
    }

    public function run(): void
    {
        foreach (self::FOLDERS as $folder => $label) {
            $path = JPATH_ROOT . '/images/' . $folder;

            if (is_dir($path)) {
                $this->exists("images/$folder");
                continue;
            }

            Folder::create($path);
            $this->created("images/$folder — $label");
        }
    }
}
