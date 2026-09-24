<?php

/**
 * @package     HospitalSantaAurora.Plugin
 * @subpackage  Console.intranet
 */

namespace HospitalSantaAurora\Plugin\Console\Intranet\Setup;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Papéis administrativos e permissões (ACL nativo), com menor privilégio.
 *
 *  Administrador                    → filho de Super Users: acesso total
 *  Qualidade e Educação Permanente  → login no painel; cria/edita/publica na Biblioteca
 *  Imprensa                         → login no painel; cria/edita/publica em Notícias, Eventos e Avisos
 *
 * Nenhum dos dois últimos pode excluir definitivamente, alterar categorias, menus,
 * módulos, usuários, ramais, sistemas ou a configuração.
 *
 * As permissões só são preenchidas quando ainda não estão definidas para o grupo:
 * um ajuste feito depois no painel (inclusive "Negado") é respeitado.
 */
final class AclStep extends AbstractStep
{
    public const GROUP_ADMIN     = 'Administrador';
    public const GROUP_QUALIDADE = 'Qualidade e Educação Permanente';
    public const GROUP_IMPRENSA  = 'Imprensa';

    private const SUPER_USERS = 8;
    private const REGISTERED  = 2;

    /** Categoria raiz => grupo que publica nela */
    public const CATEGORY_GRANTS = [
        'biblioteca' => self::GROUP_QUALIDADE,
        'noticias'   => self::GROUP_IMPRENSA,
        'eventos'    => self::GROUP_IMPRENSA,
        'avisos'     => self::GROUP_IMPRENSA,
    ];

    /** Ações para quem publica numa categoria (sem core.delete) */
    private const EDITOR_ACTIONS = ['core.create', 'core.edit', 'core.edit.own', 'core.edit.state'];

    public function title(): string
    {
        return 'Papéis e permissões';
    }

    public function run(): void
    {
        $this->ensureGroup(self::GROUP_ADMIN, self::SUPER_USERS);
        $qualidade = $this->ensureGroup(self::GROUP_QUALIDADE, self::REGISTERED);
        $imprensa  = $this->ensureGroup(self::GROUP_IMPRENSA, self::REGISTERED);

        $groups = [self::GROUP_QUALIDADE => $qualidade, self::GROUP_IMPRENSA => $imprensa];

        foreach ($groups as $label => $group) {
            // Entrar no /administrator
            $this->grant('root.1', ['core.login.admin'], $group, $label);
            // Ver a lista de artigos e preencher campos personalizados
            $this->grant('com_content', ['core.manage', 'core.edit.value'], $group, $label);
            // Gerenciador de mídia: ver e enviar arquivos (sem excluir)
            $this->grant('com_media', ['core.manage', 'core.create'], $group, $label);
        }

        // Permissões por categoria (valem para as subcategorias)
        foreach (self::CATEGORY_GRANTS as $category => $groupTitle) {
            $this->grant($this->categoryAsset($category), self::EDITOR_ACTIONS, $groups[$groupTitle], $groupTitle, "categoria $category");
        }

        $this->passwordPolicy();
    }

    private function ensureGroup(string $title, int $parentId): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__usergroups'))
            ->where($this->db->quoteName('title') . ' = :title')
            ->bind(':title', $title);

        if ($id = (int) $this->db->setQuery($query)->loadResult()) {
            $this->exists("Grupo \"$title\"");

            return $id;
        }

        $id = $this->save($this->model('com_users', 'Group'), [
            'id'        => 0,
            'title'     => $title,
            'parent_id' => $parentId,
        ], "Grupo \"$title\"");

        $this->created("Grupo \"$title\"");

        return $id;
    }

    private function categoryAsset(string $key): string
    {
        return 'com_content.category.' . $this->categoryId($key);
    }

    /** Concede as ações ao grupo no asset, sem sobrescrever o que já estiver definido. */
    private function grant(string $assetName, array $actions, int $groupId, string $groupLabel, ?string $assetLabel = null): void
    {
        $what = ($assetLabel ?? $assetName) . " → $groupLabel";

        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName(['id', 'rules']))
            ->from($this->db->quoteName('#__assets'))
            ->where($this->db->quoteName('name') . ' = :name')
            ->bind(':name', $assetName);

        $asset = $this->db->setQuery($query)->loadObject()
            ?? throw new \RuntimeException("Asset \"$assetName\" não encontrado.");

        $rules   = json_decode($asset->rules ?: '{}', true) ?: [];
        $granted = [];

        foreach ($actions as $action) {
            if (!isset($rules[$action][$groupId])) {
                $rules[$action][$groupId] = 1;
                $granted[] = $action;
            }
        }

        if (!$granted) {
            $this->exists("$what: " . implode(', ', $actions));

            return;
        }

        $json  = json_encode($rules);
        $id    = (int) $asset->id;
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__assets'))
            ->set($this->db->quoteName('rules') . ' = :rules')
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':rules', $json)
            ->bind(':id', $id, ParameterType::INTEGER);

        $this->db->setQuery($query)->execute();
        $this->changed("$what: " . implode(', ', $granted));
    }

    /** Política de senhas para os usuários do painel (§27 da especificação). */
    private function passwordPolicy(): void
    {
        $policy = [
            'allowUserRegistration' => '0',
            'minimum_length'        => '12',
            'minimum_integers'      => '1',
            'minimum_symbols'       => '1',
            'minimum_uppercase'     => '1',
            'minimum_lowercase'     => '1',
        ];

        $params  = ComponentHelper::getParams('com_users');
        $missing = array_filter($policy, static fn ($value, $key) => (int) $params->get($key) < (int) $value
            || ($key === 'allowUserRegistration' && $params->get($key) !== '0'), ARRAY_FILTER_USE_BOTH);

        if (!$missing) {
            $this->exists('Política de senhas');

            return;
        }

        $json  = (new Registry($params->toArray()))->merge(new Registry($missing))->toString();
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__extensions'))
            ->set($this->db->quoteName('params') . ' = :params')
            ->where($this->db->quoteName('element') . ' = ' . $this->db->quote('com_users'))
            ->where($this->db->quoteName('type') . ' = ' . $this->db->quote('component'))
            ->bind(':params', $json);

        $this->db->setQuery($query)->execute();
        $this->changed('Política de senhas: mínimo de 12 caracteres, com maiúscula, minúscula, número e símbolo; sem cadastro público');
    }
}
