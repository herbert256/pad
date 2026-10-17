// The board: an EventSource on the page's stream, opened in x-init. Every 'stats' event is
// JSON; 'done' says the stream is over, and the board closes the connection - without that
// EventSource would reconnect after the retry the server asked for.

document.addEventListener('alpine:init', () => {

  Alpine.data('live', (url) => ({
    source: null,
    state: 'closed',
    last: {},
    count: 0,
    loads: [],

    open() {
      this.close();
      this.source = new EventSource(url);
      this.state = 'connecting';
      this.source.onopen = () => { this.state = 'open'; };
      this.source.onerror = () => { if (this.state !== 'done') this.state = 'reconnecting'; };
      this.source.addEventListener('stats', (event) => {
        this.last = JSON.parse(event.data);
        this.count++;
        this.loads = [...this.loads, this.last.load].slice(-30);
      });
      this.source.addEventListener('done', () => { this.close(); this.state = 'done'; });
    },

    close() {
      if (this.source) this.source.close();
      this.source = null;
      this.state = 'closed';
    },

    // between the lowest and the highest load of the line, so a small change shows
    points() {
      const low = Math.min(...this.loads), high = Math.max(...this.loads);
      const span = Math.max(high - low, 0.05);
      const step = this.loads.length > 1 ? 300 / (this.loads.length - 1) : 0;
      return this.loads.map((load, at) => (at * step).toFixed(1) + ',' + (56 - (load - low) / span * 52).toFixed(1)).join(' ');
    },

    destroy() { this.close(); }
  }));

});
