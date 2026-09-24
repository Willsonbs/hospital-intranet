/**
 * Navegação do header: menu mobile, submenus (padrão disclosure) e painel de busca.
 * Sem este script o menu e a busca continuam visíveis e utilizáveis (ver template.css, html:not(.js)).
 */

const header = document.querySelector('[data-site-header]');

if (header) {
  const nav = header.querySelector('[data-site-nav]');
  const navToggle = header.querySelector('[data-nav-toggle]');
  const search = header.querySelector('[data-site-search]');
  const searchToggle = header.querySelector('[data-search-toggle]');
  const subToggles = [...header.querySelectorAll('.nav-menu__toggle')];
  const desktop = window.matchMedia('(min-width: 1100px)');

  const setOpen = (toggle, panel, open) => {
    toggle.setAttribute('aria-expanded', String(open));
    panel.classList.toggle('is-open', open);
  };

  const closeSubmenus = (except = null) => {
    subToggles.forEach((t) => {
      if (t !== except) t.setAttribute('aria-expanded', 'false');
    });
  };

  // Menu mobile
  navToggle?.addEventListener('click', () => {
    const open = navToggle.getAttribute('aria-expanded') !== 'true';
    setOpen(navToggle, nav, open);
    if (open) {
      setOpen(searchToggle, search, false);
      nav.querySelector('a, button')?.focus();
    }
  });

  // Submenus: um aberto por vez no desktop
  subToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      if (desktop.matches) closeSubmenus(toggle);
      toggle.setAttribute('aria-expanded', String(open));
    });
  });

  // No desktop o submenu do item ativo começa fechado; no mobile, aberto
  const syncActiveSubmenu = () => {
    subToggles.forEach((t) => {
      const isActive = t.closest('.nav-menu__item')?.classList.contains('is-active');
      t.setAttribute('aria-expanded', String(!desktop.matches && Boolean(isActive)));
    });
  };
  syncActiveSubmenu();
  desktop.addEventListener('change', () => {
    syncActiveSubmenu();
    if (desktop.matches) setOpen(navToggle, nav, false);
  });

  // Busca
  searchToggle?.addEventListener('click', () => {
    const open = searchToggle.getAttribute('aria-expanded') !== 'true';
    setOpen(searchToggle, search, open);
    if (open) {
      setOpen(navToggle, nav, false);
      search.querySelector('input[type="search"], input[type="text"]')?.focus();
    }
  });

  // Esc fecha o que estiver aberto e devolve o foco ao botão correspondente
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;

    const openSub = subToggles.find((t) => t.getAttribute('aria-expanded') === 'true');
    if (openSub && desktop.matches) {
      openSub.setAttribute('aria-expanded', 'false');
      openSub.focus();
    } else if (search.classList.contains('is-open')) {
      setOpen(searchToggle, search, false);
      searchToggle.focus();
    } else if (nav.classList.contains('is-open')) {
      setOpen(navToggle, nav, false);
      navToggle.focus();
    }
  });

  // Clique fora fecha dropdowns (desktop) e o menu mobile
  document.addEventListener('click', (event) => {
    if (desktop.matches && !event.target.closest('.nav-menu__item--top')) {
      closeSubmenus();
    }
    if (!desktop.matches && nav.classList.contains('is-open') && !header.contains(event.target)) {
      setOpen(navToggle, nav, false);
    }
  });

  // Foco saindo do dropdown (Tab) fecha o dropdown
  nav.addEventListener('focusout', (event) => {
    if (!desktop.matches) return;
    const item = event.target.closest('.nav-menu__item--top');
    if (item && !item.contains(event.relatedTarget)) {
      const toggle = item.querySelector('.nav-menu__toggle');
      toggle?.setAttribute('aria-expanded', 'false');
    }
  });
}
