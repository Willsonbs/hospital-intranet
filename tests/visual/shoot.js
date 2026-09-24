// Screenshots + checagem de acessibilidade (axe-core) das páginas principais.
// Uso: scripts/visual-check.sh   (roda dentro do container zenika/alpine-chrome)
const puppeteer = require('puppeteer');
const fs = require('fs');

const BASE = process.env.BASE_URL || 'http://joomla';
const OUT = process.env.OUT_DIR || '/out';
const AXE = fs.readFileSync(process.env.AXE_PATH || '/axe/axe.min.js', 'utf8');

const pages = [
  ['home', '/'],
  ['sistemas', '/sistemas'],
  ['ramais', '/ramais'],
  ['designsystem', '/?tmpl=designsystem'],
  ['404', '/pagina-inexistente'],
];
const widths = { desktop: 1440, tablet: 768, mobile: 390 };

(async () => {
  const browser = await puppeteer.launch({
    executablePath: process.env.CHROME_BIN || '/usr/bin/chromium-browser',
    args: ['--no-sandbox', '--disable-dev-shm-usage'],
  });
  const page = await browser.newPage();
  const errors = [];
  // Ignora avisos esperados no teste: COOP em HTTP e o próprio status 404 da página de erro
  const ignored = /Cross-Origin-Opener-Policy|status of 404/;
  page.on('console', (m) => m.type() === 'error' && !ignored.test(m.text()) && errors.push(`${page.url()}: ${m.text()}`));
  page.on('pageerror', (e) => errors.push(`${page.url()}: ${e.message}`));

  const report = {};
  for (const [name, path] of pages) {
    for (const [label, width] of Object.entries(widths)) {
      await page.setViewport({ width, height: 900 });
      await page.goto(BASE + path, { waitUntil: 'networkidle0' });
      // Rola até o fim para carregar imagens com loading="lazy"
      await page.evaluate(async () => {
        for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 60)); }
        window.scrollTo(0, 0);
      });
      await page.waitForNetworkIdle({ idleTime: 300 }).catch(() => {});
      await page.screenshot({ path: `${OUT}/${name}-${label}.png`, fullPage: true });
      if (label === 'desktop') {
        await page.evaluate(AXE);
        const res = await page.evaluate(() => axe.run(document, { runOnly: ['wcag2a', 'wcag2aa', 'best-practice'] }));
        report[name] = res.violations.map((v) => ({ id: v.id, impact: v.impact, n: v.nodes.length, help: v.help, target: v.nodes[0].target.join(' ') }));
      }
    }
  }

  // Interações: menu mobile aberto, dropdown desktop, busca aberta
  await page.setViewport({ width: 390, height: 900 });
  await page.goto(BASE + '/', { waitUntil: 'networkidle0' });
  await page.click('[data-nav-toggle]');
  await page.screenshot({ path: `${OUT}/menu-mobile.png` });

  await page.setViewport({ width: 1440, height: 900 });
  await page.goto(BASE + '/', { waitUntil: 'networkidle0' });
  await page.click('.nav-menu__toggle');
  await page.screenshot({ path: `${OUT}/menu-dropdown.png` });
  await page.keyboard.press('Escape');
  await page.click('[data-search-toggle]');
  const focused = await page.evaluate(() => document.activeElement.id);
  await page.screenshot({ path: `${OUT}/busca-aberta.png` });

  // Diretório de ramais: busca em tempo real, limpar, ordenar, URL
  const dir = {};
  const state = () => page.evaluate(() => ({
    count: document.querySelector('[data-directory-count]').textContent.trim(),
    visible: [...document.querySelectorAll('[data-directory-table] tbody tr')].filter((tr) => !tr.hidden).map((tr) => tr.cells[0].textContent.trim()),
    sort: [...document.querySelectorAll('th[data-sort-key]')].map((th) => `${th.dataset.sortKey}:${th.getAttribute('aria-sort')}`).join(' '),
    url: location.search,
  }));
  await page.setViewport({ width: 1440, height: 900 });
  await page.goto(BASE + '/ramais', { waitUntil: 'networkidle0' });
  await page.type('[data-directory-input]', 'enfermagem');
  await new Promise((r) => setTimeout(r, 300));
  dir.digitando = await state();
  await page.screenshot({ path: `${OUT}/ramais-busca.png` });
  await page.click('[data-directory-input]', { clickCount: 3 });
  await page.keyboard.press('Backspace');
  await new Promise((r) => setTimeout(r, 300));
  dir.limpo = await state();
  await page.click('th[data-sort-key="ramal"] button');
  dir.ordenadoRamal = await state();
  await page.click('th[data-sort-key="ramal"] button');
  dir.ordenadoRamalDesc = await state();
  await page.type('[data-directory-input]', 'xyz');
  await new Promise((r) => setTimeout(r, 300));
  dir.semResultado = await state();
  await page.screenshot({ path: `${OUT}/ramais-vazio.png` });
  ['digitando', 'limpo', 'ordenadoRamal', 'ordenadoRamalDesc', 'semResultado'].forEach((k) => { dir[k].visible = `${dir[k].visible.length} linhas: ${dir[k].visible.slice(0, 3).join(', ')}`; });

  fs.writeFileSync(`${OUT}/report.json`, JSON.stringify({ axe: report, consoleErrors: errors, searchFocus: focused, ramais: dir }, null, 2));
  await browser.close();
})().catch((e) => { console.error(e); process.exit(1); });
