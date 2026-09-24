/**
 * Filtro de listas em tempo real (Sistemas, Documentos, Protocolos).
 *
 * Marcação:
 *   <div data-filter-root>
 *     <form data-filter-form> <input name="q" data-filter="q"> <select name="tipo" data-filter="tipo"> … </form>
 *     <p data-filter-count data-one="1 item" data-many="%d itens" data-none="Nenhum item"></p>
 *     <section data-filter-group> … <li data-filter-item data-q="texto normalizado" data-tipo="12"> … </section>
 *     <div data-filter-empty hidden> … </div>
 *   </div>
 *
 * "q": todas as palavras precisam aparecer em data-q (sem acentos, minúsculas).
 * Demais filtros: igualdade com data-<nome>. O servidor aplica as mesmas regras
 * (parâmetros GET) para quem está sem JavaScript.
 */

const normalize = (text) => text.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().replace(/\s+/g, ' ').trim();

document.querySelectorAll('[data-filter-root]').forEach((root) => {
  const form = root.querySelector('[data-filter-form]');
  const controls = [...root.querySelectorAll('[data-filter]')];
  const items = [...root.querySelectorAll('[data-filter-item]')];
  const groups = [...root.querySelectorAll('[data-filter-group]')];
  const count = root.querySelector('[data-filter-count]');
  const empty = root.querySelector('[data-filter-empty]');

  const apply = () => {
    const values = Object.fromEntries(controls.map((c) => [c.dataset.filter, c.value.trim()]));
    const terms = normalize(values.q || '').split(' ').filter(Boolean);
    let visible = 0;

    items.forEach((item) => {
      const match = terms.every((t) => (item.dataset.q || '').includes(t))
        && Object.entries(values).every(([key, value]) => key === 'q' || value === '' || item.dataset[key] === value);
      item.hidden = !match;
      if (match) visible += 1;
    });

    groups.forEach((group) => {
      group.hidden = !group.querySelector('[data-filter-item]:not([hidden])');
    });

    if (count) {
      count.textContent = visible === 0 ? count.dataset.none
        : visible === 1 ? count.dataset.one : count.dataset.many.replace('%d', visible);
    }
    if (empty) empty.hidden = visible !== 0;

    const url = new URL(window.location.href);
    Object.entries(values).forEach(([k, v]) => (v ? url.searchParams.set(k, v) : url.searchParams.delete(k)));
    url.searchParams.delete('limitstart');
    window.history.replaceState(null, '', url);
  };

  let timer;
  controls.forEach((control) => {
    const event = control.tagName === 'SELECT' ? 'change' : 'input';
    control.addEventListener(event, () => {
      clearTimeout(timer);
      timer = setTimeout(apply, event === 'input' ? 120 : 0);
    });
  });

  form?.addEventListener('submit', (event) => {
    event.preventDefault();
    clearTimeout(timer);
    apply();
  });

  form?.addEventListener('reset', () => setTimeout(apply, 0));
});
