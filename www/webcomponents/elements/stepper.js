// <pad-stepper>: a form-associated custom element. ElementInternals hands the form its value,
// so a post of the {form} carries name=value like an <input> - and a reset puts it back.

class PadStepper extends HTMLElement {

  static formAssociated = true;

  constructor() {
    super();
    this.internals = this.attachInternals();
  }

  connectedCallback() {
    this.initial = this.value;
    const [less, more] = this.shadowRoot.querySelectorAll('button');
    less.addEventListener('click', () => this.step(-1));
    more.addEventListener('click', () => this.step(1));
    this.show();
  }

  get value() { return Number(this.getAttribute('value')) || 0; }
  get min() { return Number(this.getAttribute('min') ?? 0); }
  get max() { return Number(this.getAttribute('max') ?? 99); }

  step(by) {
    this.setAttribute('value', Math.min(this.max, Math.max(this.min, this.value + by)));
    this.show();
  }

  show() {
    this.shadowRoot.querySelector('output').textContent = this.value;
    this.internals.setFormValue(String(this.value));
  }

  formResetCallback() {
    this.setAttribute('value', this.initial);
    this.show();
  }
}

customElements.define('pad-stepper', PadStepper);
