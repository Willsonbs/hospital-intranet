/**
 * Intranet Hospitalar — script principal do template.
 */
import { initNavigation } from './navigation.js';

function greetingFor(hour) {
	if (hour < 5) return 'Boa noite!';
	if (hour < 12) return 'Bom dia!';
	if (hour < 18) return 'Boa tarde!';
	return 'Boa noite!';
}

function initGreeting() {
	const el = document.querySelector('[data-greeting]');
	if (el) el.textContent = greetingFor(new Date().getHours());
}

initGreeting();
initNavigation();

// Módulos carregados só nas páginas que precisam deles
const directory = document.querySelector('[data-directory]');
if (directory) {
	import('./directory.js').then(({ initDirectory }) => initDirectory(directory));
}
