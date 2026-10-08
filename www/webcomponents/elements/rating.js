// <pad-rating>: the stars are in the shadow root PAD rendered, lit by the value attribute. A
// click sets the attribute and posts the vote to the page with the session's CSRF token; the
// text in the light DOM becomes the average PAD answers.

class PadRating extends HTMLElement {

  static observedAttributes = ['value'];

  connectedCallback() {
    this.shadowRoot.addEventListener('click', (event) => {
      const star = event.target.closest('button[data-value]');
      if (star) this.vote(Number(star.dataset.value));
    });
  }

  attributeChangedCallback(name, before, now) {
    this.shadowRoot?.querySelectorAll('button').forEach((star) =>
      star.setAttribute('aria-pressed', String(Number(star.dataset.value) <= Number(now))));
  }

  async vote(value) {
    this.setAttribute('value', value);
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const response = await fetch('?examples/rating&padFormat=json', {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-Token': token },
      body: new URLSearchParams({ id: this.getAttribute('name'), value })
    });
    if (!response.ok) return;
    const { rating } = await response.json();
    this.textContent = `${rating.average} on average, ${rating.votes} votes - yours: ${value}`;
  }
}

customElements.define('pad-rating', PadRating);
