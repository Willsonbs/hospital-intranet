/**
 * Atualiza a saudação do hero ("Bom dia!") pelo relógio do navegador.
 * O servidor já entrega o texto correto; isto só corrige páginas vindas do cache.
 */

const el = document.querySelector('[data-greeting]');

if (el) {
  const hour = new Date().getHours();
  let key = 'evening';
  if (hour >= 5 && hour < 12) key = 'morning';
  else if (hour >= 12 && hour < 18) key = 'afternoon';

  const text = el.dataset[key];
  if (text && el.textContent.trim() !== text) el.textContent = text;
}
