/**
 * Comportamento dos componentes do design system.
 *
 * Dropdown:  <div class="dropdown"><button data-dropdown-toggle aria-expanded="false" aria-controls="m1">…</button>
 *            <ul class="dropdown__menu" id="m1" hidden>…</ul></div>
 * Modal:     <button data-modal-open="dlg1">…</button> <dialog class="modal" id="dlg1">… <button data-modal-close>…</button></dialog>
 */

// --- Dropdown ---------------------------------------------------------------

const closeDropdown = (toggle) => {
  toggle.setAttribute('aria-expanded', 'false');
  document.getElementById(toggle.getAttribute('aria-controls'))?.setAttribute('hidden', '');
};

document.addEventListener('click', (event) => {
  const toggle = event.target.closest('[data-dropdown-toggle]');

  document.querySelectorAll('[data-dropdown-toggle][aria-expanded="true"]').forEach((open) => {
    if (open !== toggle && !open.parentElement.contains(event.target)) closeDropdown(open);
  });

  if (!toggle) return;

  const menu = document.getElementById(toggle.getAttribute('aria-controls'));
  const open = toggle.getAttribute('aria-expanded') !== 'true';
  toggle.setAttribute('aria-expanded', String(open));
  menu?.toggleAttribute('hidden', !open);
  if (open) menu?.querySelector('a, button')?.focus();
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  const open = document.querySelector('[data-dropdown-toggle][aria-expanded="true"]');
  if (open) {
    closeDropdown(open);
    open.focus();
  }
});

// --- Modal (<dialog> nativo: foco preso e Esc já vêm do navegador) ---------

document.addEventListener('click', (event) => {
  const opener = event.target.closest('[data-modal-open]');
  if (opener) {
    document.getElementById(opener.dataset.modalOpen)?.showModal();
    return;
  }

  const closer = event.target.closest('[data-modal-close]');
  if (closer) {
    closer.closest('dialog')?.close();
    return;
  }

  // Clique no fundo escuro fecha
  if (event.target instanceof HTMLDialogElement && event.target.classList.contains('modal')) {
    event.target.close();
  }
});
