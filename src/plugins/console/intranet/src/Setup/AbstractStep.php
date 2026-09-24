<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Symfony\Component\Console\Style\SymfonyStyle;

\defined('_JEXEC') or die;

/**
 * Base dos passos do setup.
 *
 * Todo registro criado recebe a nota "intranet:<chave>", usada para encontrá-lo
 * nas execuções seguintes (títulos e aliases podem ser alterados no painel).
 */
abstract class AbstractStep
{
    public function __construct(
        protected ConsoleApplication $app,
        protected DatabaseInterface $db,
        protected SymfonyStyle $io
    ) {
    }

    abstract public function title(): string;

    abstract public function run(): void;

    /** Cria um model administrativo novo (um por gravação: evita estado residual). */
    protected function model(string $component, string $name): AdminModel
    {
        return $this->app->bootComponent($component)->getMVCFactory()
            ->createModel($name, 'Administrator', ['ignore_request' => true]);
    }

    /** Grava pelo model e devolve o id; lança exceção com a mensagem do Joomla em caso de erro. */
    protected function save(AdminModel $model, array $data, string $label): int
    {
        if (!$model->save($data)) {
            throw new \RuntimeException(\sprintf('Falha ao criar "%s": %s', $label, $model->getError()));
        }

        return (int) $model->getState($model->getName() . '.id');
    }

    protected function note(string $key): string
    {
        return 'intranet:' . $key;
    }

    /** Id do registro com a nota "intranet:<chave>", ou null. */
    protected function findByNote(string $table, string $key, array $where = []): ?int
    {
        $note  = $this->note($key);
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName($table))
            ->where($this->db->quoteName('note') . ' = :note')
            ->bind(':note', $note);

        foreach ($where as $column => $value) {
            $query->where($this->db->quoteName($column) . ' = ' . $this->db->quote($value));
        }

        $id = $this->db->setQuery($query)->loadResult();

        return $id === null ? null : (int) $id;
    }

    /** Id de uma categoria criada pelo setup (ex.: "noticias", "biblioteca/protocolos"). */
    protected function categoryId(string $key): int
    {
        return $this->findByNote('#__categories', 'cat:' . $key, ['extension' => 'com_content'])
            ?? throw new \RuntimeException("Categoria \"$key\" não encontrada. Rode o passo de categorias antes.");
    }

    protected function extensionId(string $element, string $type = 'component'): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('extension_id'))
            ->from($this->db->quoteName('#__extensions'))
            ->where($this->db->quoteName('element') . ' = :element')
            ->where($this->db->quoteName('type') . ' = :type')
            ->bind(':element', $element)
            ->bind(':type', $type);

        return (int) $this->db->setQuery($query)->loadResult();
    }

    /** Atualiza colunas de um registro diretamente (para ajustes em registros do núcleo). */
    protected function update(string $table, int $id, array $values): void
    {
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName($table))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        foreach ($values as $column => $value) {
            $query->set($this->db->quoteName($column) . ' = ' . $this->db->quote((string) $value));
        }

        $this->db->setQuery($query)->execute();
    }

    protected function created(string $what): void
    {
        $this->io->writeln("  <info>+</info> $what");
    }

    protected function exists(string $what): void
    {
        $this->io->writeln("  <comment>=</comment> $what <comment>(já existe)</comment>");
    }

    protected function changed(string $what): void
    {
        $this->io->writeln("  <info>~</info> $what");
    }
}
