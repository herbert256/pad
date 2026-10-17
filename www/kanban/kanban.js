// Kanban in the browser:
//
//   drag and drop   a card dropped in a column - before the card under the pointer, or at its
//                   end - is posted as JSON to ?move&padFormat=json with the session's token
//                   in X-CSRF-Token; the arrow buttons do the same from the keyboard
//   fragments       after a move, and whenever the board moved on elsewhere, the columns and
//                   the activity are asked for again: ?index&padFragment=columns
//   live            an EventSource on ?events hears when the board's version went up - a card
//                   moved in another window - and refreshes the board
//   new card        the New card button opens the dialog; without this it is a page
//
// The same requests on a server and in the browser, where the application packed by pad pack
// runs on PHP compiled to WebAssembly: the service worker answers them for every window, so
// two windows of the installed application keep each other up to date.

(function () {

  'use strict';

  document.documentElement.classList.add('js');

  const token   = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const dialog  = document.getElementById('new-card');
  const opener  = document.querySelector('[data-new-card]');

  if (dialog && opener) {
    opener.addEventListener('click', event => { event.preventDefault(); dialog.showModal(); });
    dialog.querySelector('[data-close]').addEventListener('click', () => dialog.close());
    // A card that came back with a message is shown again, as the modal it was.
    if (dialog.open) { dialog.close(); dialog.showModal(); }
  }

  const board   = document.getElementById('board');
  const columns = document.getElementById('columns');

  markLate();

  if (!board || !columns) return;

  let version  = Number(board.dataset.version) || 0;
  let dragging = null;
  let busy     = false;

  async function move(card, column, before) {
    const response = await fetch('?move&padFormat=json', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': token },
      body: JSON.stringify({ card: Number(card), column: Number(column), before: Number(before) || 0 })
    });
    const answer = await response.json();
    if (answer.version) version = Math.max(version, answer.version);
    await refresh(card);
  }

  async function refresh(arrived) {
    if (busy) return;
    busy = true;
    try {
      const [cards, activity] = await Promise.all([
        fetch('?index&padFragment=columns').then(response => response.text()),
        fetch('?index&padFragment=activity').then(response => response.text())
      ]);
      if (dragging) return;
      const focused = document.activeElement && document.activeElement.closest('.card');
      columns.innerHTML = cards;
      const feed = document.getElementById('activity');
      const first = feed.firstElementChild && feed.firstElementChild.textContent;
      feed.innerHTML = activity;
      if (feed.firstElementChild && feed.firstElementChild.textContent !== first) feed.firstElementChild.classList.add('fresh');
      markLate();
      const card = arrived && columns.querySelector(`[data-card="${arrived}"]`);
      if (card) {
        card.classList.add('arrived');
        if (focused) card.focus();
      }
    } finally {
      busy = false;
    }
  }

  // Due dates gone by, by this browser's clock.

  function markLate() {
    const today = new Date().toISOString().slice(0, 10);
    for (const time of document.querySelectorAll('.card time[datetime]'))
      time.classList.toggle('late', time.getAttribute('datetime') < today && !time.closest('[data-column="4"]'));
  }

  // ---- drag and drop -------------------------------------------------------------------

  function below(zone, y) {
    return [...zone.querySelectorAll('.card:not(.dragging)')].find(card => {
      const box = card.getBoundingClientRect();
      return y < box.top + box.height / 2;
    });
  }

  columns.addEventListener('dragstart', event => {
    const card = event.target.closest && event.target.closest('.card');
    if (!card) return;
    dragging = card;
    card.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', card.dataset.card);
  });

  columns.addEventListener('dragend', () => {
    if (dragging) dragging.classList.remove('dragging');
    dragging = null;
    for (const zone of columns.querySelectorAll('.target')) zone.classList.remove('target');
  });

  columns.addEventListener('dragover', event => {
    const zone = event.target.closest && event.target.closest('[data-drop]');
    if (!zone || !dragging) return;
    event.preventDefault();
    for (const other of columns.querySelectorAll('.target')) if (other !== zone) other.classList.remove('target');
    zone.classList.add('target');
    const next = below(zone, event.clientY);
    if (next) zone.insertBefore(dragging, next); else zone.appendChild(dragging);
  });

  columns.addEventListener('drop', event => {
    const zone = event.target.closest && event.target.closest('[data-drop]');
    if (!zone || !dragging) return;
    event.preventDefault();
    const card = dragging;
    const next = card.nextElementSibling;
    card.classList.remove('dragging');
    dragging = null;
    zone.classList.remove('target');
    move(card.dataset.card, zone.dataset.drop, next && next.dataset.card);
  });

  // ---- the arrows: the same move from the keyboard ----------------------------------------

  columns.addEventListener('click', event => {
    const button = event.target.closest && event.target.closest('[data-move]');
    if (!button) return;
    event.preventDefault();
    const card = button.closest('.card');
    card.focus();
    move(card.dataset.card, button.dataset.move, 0);
  });

  // ---- live: the board moved on elsewhere ---------------------------------------------------

  if ('EventSource' in window) {
    const live = document.getElementById('live');
    const source = new EventSource('?events');
    source.addEventListener('open', () => { if (live) live.hidden = false; });
    source.addEventListener('board', event => {
      let data = {};
      try { data = JSON.parse(event.data); } catch (e) { return; }
      if (!(data.version > version)) return;
      version = data.version;
      refresh();
      if (live) {
        live.querySelector('span').textContent = data.latest ? `Live - ${data.latest}` : 'Live - updated';
        live.classList.add('updated');
        clearTimeout(live.timer);
        live.timer = setTimeout(() => { live.querySelector('span').textContent = 'Live'; live.classList.remove('updated'); }, 4000);
      }
    });
  }

})();
