// <pad-timeline>, a Lit element: rendered in the browser, fed by PAD through the items
// attribute - JSON the ^ sigil wrote, read into the items property by Lit's Array converter.

import { LitElement, html, css } from 'https://cdn.jsdelivr.net/npm/lit@3/+esm';

class PadTimeline extends LitElement {

  static properties = {
    items: { type: Array },
    open: { state: true }
  };

  static styles = css`
    :host { display: block; }
    ol { list-style: none; margin: 0; padding: 0 0 0 22px; border-left: 2px solid var(--line, #ddd); }
    li { position: relative; margin: 0 0 14px; padding: 12px 16px; border-radius: 12px; background: var(--surface-2, #f4f4f4); cursor: pointer; }
    li::before { content: ''; position: absolute; left: -30px; top: 16px; width: 12px; height: 12px; border-radius: 50%; background: var(--accent, #e8590c); }
    li.is-open { outline: 2px solid var(--accent, #e8590c); }
    .year { font: 800 13px/1 system-ui, sans-serif; color: var(--accent, #e8590c); }
  `;

  constructor() {
    super();
    this.items = [];
    this.open = -1;
  }

  render() {
    return html`
      <ol>
        ${this.items.map((item, at) => html`
          <li class=${at === this.open ? 'is-open' : ''} @click=${() => { this.open = at === this.open ? -1 : at; }}>
            <div class="year">${item.year}</div>
            <div>${item.what}</div>
          </li>`)}
      </ol>`;
  }
}

customElements.define('pad-timeline', PadTimeline);
