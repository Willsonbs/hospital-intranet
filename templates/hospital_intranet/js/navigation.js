/**
 * Menu hamburger (mobile): abre/fecha, fecha com Esc e ao clicar fora.
 */
export function initNavigation() {
	const toggle = document.querySelector('.hi-nav-toggle');
	const nav = document.getElementById(toggle?.getAttribute('aria-controls') ?? '');
	if (!toggle || !nav) return;

	const setOpen = (open) => {
		nav.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', String(open));
		toggle.querySelector('i')?.classList.replace(open ? 'fa-bars' : 'fa-xmark', open ? 'fa-xmark' : 'fa-bars');
	};

	toggle.addEventListener('click', () => setOpen(!nav.classList.contains('is-open')));

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && nav.classList.contains('is-open')) {
			setOpen(false);
			toggle.focus();
		}
	});

	document.addEventListener('click', (event) => {
		if (nav.classList.contains('is-open') && !nav.contains(event.target) && !toggle.contains(event.target)) {
			setOpen(false);
		}
	});
}
