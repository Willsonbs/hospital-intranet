/**
 * Diretório de ramais: pesquisa em tempo real e ordenação por coluna, no navegador.
 * Sem JavaScript, o mesmo formulário e os links de ordenação funcionam pelo servidor.
 */
const normalize = (text) =>
	text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

const collator = new Intl.Collator('pt-BR', { numeric: true, sensitivity: 'base' });

export function initDirectory(root) {
	const input = root.querySelector('[data-directory-search]');
	const form = root.querySelector('[data-directory-form]');
	const table = root.querySelector('[data-directory-table]');
	const tbody = table?.tBodies[0];
	const count = root.querySelector('[data-directory-count]');
	const empty = root.querySelector('[data-directory-empty]');
	if (!input || !tbody) return;

	const rows = [...tbody.rows].map((row) => ({
		row,
		haystack: normalize(row.textContent),
		values: Object.fromEntries([...row.cells].map((cell) => [cell.dataset.col, cell.textContent.trim()])),
	}));

	const updateCount = (visible) => {
		if (!count) return;
		count.textContent = visible === 1 ? count.dataset.one : count.dataset.many.replace('%d', visible);
	};

	const filter = () => {
		const terms = normalize(input.value).split(/\s+/).filter(Boolean);
		let visible = 0;
		for (const item of rows) {
			const match = terms.every((term) => item.haystack.includes(term));
			item.row.hidden = !match;
			if (match) visible++;
		}
		updateCount(visible);
		if (empty) empty.hidden = visible > 0;
		table.hidden = visible === 0;
	};

	const sortBy = (column, direction) => {
		const factor = direction === 'ascending' ? 1 : -1;
		rows.sort((a, b) => factor * collator.compare(a.values[column] ?? '', b.values[column] ?? ''));
		tbody.append(...rows.map((item) => item.row));
		for (const th of table.tHead.rows[0].cells) {
			th.setAttribute('aria-sort', th.querySelector('[data-sort]')?.dataset.sort === column ? direction : 'none');
		}
	};

	input.addEventListener('input', filter);
	form?.addEventListener('submit', (event) => {
		event.preventDefault();
		filter();
	});

	table.tHead.addEventListener('click', (event) => {
		const trigger = event.target.closest('[data-sort]');
		if (!trigger) return;
		event.preventDefault();
		const th = trigger.closest('th');
		const next = th.getAttribute('aria-sort') === 'ascending' ? 'descending' : 'ascending';
		sortBy(trigger.dataset.sort, next);
	});

	// Links com role="button" também respondem à barra de espaço
	table.tHead.addEventListener('keydown', (event) => {
		if (event.key === ' ' && event.target.matches('[data-sort]')) {
			event.preventDefault();
			event.target.click();
		}
	});

	if (input.value) filter();
}
