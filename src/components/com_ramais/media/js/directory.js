/**
 * Diretório de ramais: busca em tempo real e ordenação sem recarregar a página.
 * Sem este script a página continua funcionando pelo servidor (?q=, ?sort=, ?dir=).
 */

const root = document.querySelector('[data-directory]');

if (root) {
  const form = root.querySelector('[data-directory-form]');
  const input = root.querySelector('[data-directory-input]');
  const table = root.querySelector('[data-directory-table]');
  const tbody = table.tBodies[0];
  const wrap = root.querySelector('[data-directory-wrap]');
  const empty = root.querySelector('[data-directory-empty]');
  const count = root.querySelector('[data-directory-count]');
  const headers = [...table.querySelectorAll('th[data-sort-key]')];

  // Sem acentos e sem diferença de maiúsculas: "farmacia" encontra "Farmácia"
  const normalize = (text) => text.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim();
  const collator = new Intl.Collator('pt-BR', { sensitivity: 'base', numeric: true });

  const rows = [...tbody.rows].map((tr) => ({
    tr,
    cells: [...tr.cells].map((td) => td.textContent.trim()),
    haystack: normalize(tr.textContent),
  }));

  const keyIndex = Object.fromEntries(headers.map((th, i) => [th.dataset.sortKey, i]));

  const updateUrl = (params) => {
    const url = new URL(window.location.href);
    Object.entries(params).forEach(([k, v]) => (v ? url.searchParams.set(k, v) : url.searchParams.delete(k)));
    window.history.replaceState(null, '', url);
  };

  const setCount = (n) => {
    const text = n === 0 ? count.dataset.none : n === 1 ? count.dataset.one : count.dataset.many.replace('%d', n);
    count.textContent = text;
  };

  // Busca: todas as palavras precisam aparecer (em qualquer coluna)
  const filter = () => {
    const terms = normalize(input.value).split(/\s+/).filter(Boolean);
    let visible = 0;

    rows.forEach((row) => {
      const match = terms.every((t) => row.haystack.includes(t));
      row.tr.hidden = !match;
      if (match) visible += 1;
    });

    wrap.hidden = visible === 0;
    empty.hidden = visible !== 0;
    setCount(visible);
    updateUrl({ q: input.value.trim() });
  };

  let timer;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(filter, 120);
  });

  // Enter não recarrega a página: o filtro já está aplicado
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    clearTimeout(timer);
    filter();
  });

  // Ordenação pelo cabeçalho (botão; o link continua funcionando sem JS)
  const sortBy = (key, dir) => {
    const i = keyIndex[key];
    const sorted = [...rows].sort((a, b) => {
      const result = collator.compare(a.cells[i], b.cells[i]) || collator.compare(a.cells[0], b.cells[0]);
      return dir === 'desc' ? -result : result;
    });

    sorted.forEach((row) => tbody.appendChild(row.tr));
    headers.forEach((th) => {
      th.setAttribute('aria-sort', th.dataset.sortKey === key ? (dir === 'asc' ? 'ascending' : 'descending') : 'none');
    });
    updateUrl({ sort: key === 'setor' && dir === 'asc' ? '' : key, dir: key === 'setor' && dir === 'asc' ? '' : dir });
  };

  headers.forEach((th) => {
    const link = th.querySelector('[data-sort-button]');
    const button = document.createElement('button');
    button.type = 'button';
    button.className = link.className;
    button.innerHTML = link.innerHTML;
    link.replaceWith(button);

    button.addEventListener('click', () => {
      const current = th.getAttribute('aria-sort');
      sortBy(th.dataset.sortKey, current === 'ascending' ? 'desc' : 'asc');
    });
  });

  // Estado inicial vindo da URL (busca compartilhada por link)
  if (input.value.trim()) filter();
}
