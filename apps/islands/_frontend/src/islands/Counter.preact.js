// Preact without a compiler: htm reads the template literal at run time.
import { html } from 'htm/preact';
import { useState } from 'preact/hooks';

export default function Counter({ start, framework }) {
  const [count, setCount] = useState(start);
  return html`
    <div class="counter">
      <div class="counter-value">${count}</div>
      <div class="counter-buttons">
        <button onClick=${() => setCount(count - 1)} aria-label="One less">−</button>
        <button onClick=${() => setCount(count + 1)} aria-label="One more">+</button>
      </div>
      <p class="counter-note">${framework}: useState, no build step</p>
    </div>`;
}
