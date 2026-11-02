document.addEventListener('click', async event => {
  const button = event.target.closest('[data-demo-copy]');
  if (!button || !navigator.clipboard) return;
  const source = document.getElementById(button.dataset.demoCopy);
  if (!source) return;
  try {
    await navigator.clipboard.writeText(source.textContent);
    button.textContent = 'Copied';
    setTimeout(() => { button.textContent = 'Copy'; }, 1400);
  } catch { button.textContent = 'Select and copy'; }
});
