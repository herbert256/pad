// The search: its first result is the argument PAD wrote - search({^result}) - and every
// later query asks the same page for JSON. pad.get is www/alpine/alpine-app.js: it adds
// padFormat=json and logs the request under the example.

document.addEventListener('alpine:init', () => {

  Alpine.data('search', (first) => ({
    q: first.q,
    count: first.count,
    items: first.items,
    busy: false,
    asked: 0,

    async ask() {
      const mine = ++this.asked;
      this.busy = true;
      try {
        const answer = await pad.get('examples/search', { q: this.q });
        if (mine !== this.asked) return;        // an older answer that came in late
        this.count = answer.result.count;
        this.items = answer.result.items;
      } finally {
        if (mine === this.asked) this.busy = false;
      }
    }
  }));

});
