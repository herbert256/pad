// Ledger in the browser: the overview's quick entry booked without leaving the page. The form
// is sent as JSON - fetch('?quick&padFormat=json'), the session's token in X-CSRF-Token - and
// PAD answers with what quick.php exposes: the booking and the month's new totals, which go
// into the cards and the list of the latest entries. Without this the form posts as a form.
//
// On a server, or on PHP compiled to WebAssembly in the browser when the application is
// packed with pad pack - the request is the same either way.

(function () {

  'use strict';

  const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const form  = document.querySelector('.quick-form');

  if (!form) return;

  const said = form.querySelector('.said');

  form.addEventListener('submit', async event => {
    event.preventDefault();
    const fields = Object.fromEntries(new FormData(form).entries());
    said.className = 'said';
    said.textContent = 'Booking…';
    let answer;
    try {
      const response = await fetch('?quick&padFormat=json', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': token },
        body: JSON.stringify({ amount: fields.amount, category: fields.category, note: fields.note, month: fields.month })
      });
      answer = await response.json();
    } catch (error) {
      answer = { ok: false, error: 'The entry could not be sent.' };
    }
    if (!answer.ok) {
      said.className = 'said bad';
      said.textContent = answer.error || 'Not booked.';
      return;
    }
    // Booked in another month than the one shown - today, the first entry of a new month:
    // the overview of that month.
    if (answer.month && answer.month !== fields.month) {
      location.assign('?index&month=' + encodeURIComponent(answer.month));
      return;
    }
    for (const [key, value] of Object.entries(answer.shown || {})) {
      const cell = document.querySelector(`[data-kpi="${key}"]`);
      if (!cell || cell.textContent === value) continue;
      cell.textContent = value;
      cell.classList.remove('changed');
      void cell.offsetWidth;
      cell.classList.add('changed');
    }
    const list = document.querySelector('.latest');
    if (list && answer.entry) {
      const item = document.createElement('li');
      item.className = 'fresh';
      const day = new Date(answer.entry.day + 'T12:00:00');
      item.innerHTML = '<span class="day"></span><span class="what"><span></span><small></small></span><b class="neg"></b>';
      item.querySelector('.day').textContent = day.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
      item.querySelector('.what span').textContent = answer.entry.note || answer.entry.category;
      item.querySelector('small').textContent = answer.entry.category;
      item.querySelector('b').textContent = '−€ ' + Math.abs(answer.entry.amount).toLocaleString('en', { minimumFractionDigits: 2 });
      const empty = list.querySelector('li.dim');
      if (empty) empty.remove();
      list.prepend(item);
    }
    said.textContent = `Booked: ${fields.amount} on ${form.querySelector('select option:checked').textContent}.`;
    form.reset();
    form.querySelector('[name=amount]').focus();
  });

})();
