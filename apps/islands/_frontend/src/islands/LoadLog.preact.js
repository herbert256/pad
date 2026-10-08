// The log of the loading page, in Preact: every island that mounted before it, and every
// one after (the pad:island event of pad-islands.js), with the time its code took to come.
import { html } from 'htm/preact';
import { useState, useEffect } from 'preact/hooks';
import { mounted } from '../pad-islands.js';

export default function LoadLog() {
  const [rows, setRows] = useState(mounted);
  useEffect(() => {
    setRows(mounted());                                    // what came between render and now
    const told = (event) => setRows((now) => [...now, event.detail]);
    window.addEventListener('pad:island', told);
    return () => window.removeEventListener('pad:island', told);
  }, []);
  return html`
    <ol class="load-log">
      ${rows.length === 0 && html`<li class="muted">Waiting for the first island ...</li>`}
      ${rows.map((row) => html`<li class="rise"><code>${row.at.toLocaleTimeString()}</code> <strong>${row.name}</strong> mounted - its code took ${row.ms} ms <span class="badge">${row.element.getAttribute('data-load') || 'eager'}</span></li>`)}
    </ol>`;
}
