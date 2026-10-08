// Three components, registered before Alpine starts. Each is a function the template calls
// in x-data, with PAD's data as the argument: accordion({^faq}), tabs({^tabs}), dialog().
// This file is sent by the web server as it is - its braces never meet the PAD parser.

document.addEventListener('alpine:init', () => {

  // Which question is open - the index PAD's {current@faq} wrote, starting at 1.
  Alpine.data('accordion', (items) => ({
    items,
    open: 1,
    isOpen(at) { return this.open === at; },
    toggle(at) { this.open = this.open === at ? 0 : at; }
  }));

  Alpine.data('tabs', (list) => ({
    list,
    active: list[0].key,
    current() { return this.list.find(tab => tab.key === this.active); }
  }));

  Alpine.data('dialog', () => ({
    open: false,
    show() {
      this.open = true;
      this.$nextTick(() => this.$refs.close.focus());
    },
    hide() { this.open = false; }
  }));

});
