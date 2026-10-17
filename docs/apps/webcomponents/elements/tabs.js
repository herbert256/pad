// <pad-tabs>: the shadow root is the one PAD rendered - this only adds behaviour. A click
// or an arrow key picks a tab; its panel shows, the others get hidden.

class PadTabs extends HTMLElement {

  connectedCallback() {
    this.tabs = [...this.querySelectorAll('[slot="tab"]')];
    this.panels = [...this.querySelectorAll('[slot="panel"]')];
    this.addEventListener('click', (event) => {
      const tab = event.target.closest('[slot="tab"]');
      if (tab) this.pick(this.tabs.indexOf(tab));
    });
    this.addEventListener('keydown', (event) => {
      const at = this.tabs.indexOf(document.activeElement);
      if (at < 0) return;
      const step = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
      if (!step) return;
      event.preventDefault();
      this.pick((at + step + this.tabs.length) % this.tabs.length, true);
    });
    this.pick(0);
  }

  pick(at, focus) {
    this.tabs.forEach((tab, index) => {
      tab.setAttribute('aria-selected', String(index === at));
      tab.tabIndex = index === at ? 0 : -1;
    });
    this.panels.forEach((panel, index) => { panel.hidden = index !== at; });
    if (focus) this.tabs[at].focus();
  }
}

customElements.define('pad-tabs', PadTabs);
