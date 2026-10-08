// The theme switch of the top bar - light or dark, remembered in localStorage. The elements
// each have a module of their own in elements/, loaded by the page that uses them.

const key = 'pad-wc-theme';

function stored() {
  try { return localStorage.getItem(key); } catch (e) { return null; }
}

function apply(theme) {
  if (theme === 'light' || theme === 'dark') document.documentElement.setAttribute('data-theme', theme);
}

apply(stored());

document.addEventListener('click', (event) => {
  if (!event.target.closest('[data-theme-toggle]')) return;
  const now = document.documentElement.getAttribute('data-theme') ||
              (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  const next = now === 'dark' ? 'light' : 'dark';
  apply(next);
  try { localStorage.setItem(key, next); } catch (e) { /* private window */ }
});
