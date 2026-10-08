// The list: what PAD wrote into the argument - todos({^todos}) - and from then on what PAD
// answers. A change shows at once; the post that follows answers the whole list, which
// replaces the component's own. If the post fails, the server's list stands.

document.addEventListener('alpine:init', () => {

  Alpine.data('todos', (first) => ({
    list: first,
    text: '',
    syncing: false,

    done() { return this.list.filter(todo => todo.done).length; },
    progress() { return this.list.length ? Math.round(this.done() / this.list.length * 100) : 0; },

    async sync(body) {
      this.syncing = true;
      try {
        const answer = await pad.post('examples/todos', body);
        this.list = answer.todos;
      } catch (error) {
        const answer = await pad.get('examples/todos');
        this.list = answer.todos;
      } finally {
        this.syncing = false;
      }
    },

    add() {
      const text = this.text.trim();
      if (!text) return;
      this.list.push({ id: 'new', text, done: false });
      this.text = '';
      this.sync({ op: 'add', text });
    },

    toggle(todo) {
      todo.done = !todo.done;
      this.sync({ op: 'toggle', id: todo.id });
    },

    remove(todo) {
      this.list = this.list.filter(one => one !== todo);
      this.sync({ op: 'remove', id: todo.id });
    },

    clear() {
      this.list = this.list.filter(todo => !todo.done);
      this.sync({ op: 'clear' });
    }
  }));

});
