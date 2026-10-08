// The form: the rules are the page's PHP's, exported by padValidateClient into the argument
// - signup({^start}) - and padValidate, the checker {validator} brought, judges them in the
// browser with the server's own messages. Untick "check in the browser" and the post goes as
// it is: the server's answer holds the same messages.

document.addEventListener('alpine:init', () => {

  Alpine.data('signup', (start) => ({
    rules: start.rules,
    people: start.people,
    form: { name: '', email: '', team: '', seats: '' },
    errors: {},
    browser: true,
    busy: false,
    note: '',

    leave(name) {
      if (this.browser && this.form[name] !== '')
        this.errors[name] = padValidate.field(this.rules[name], this.form[name], this.form);
    },

    async send() {
      this.note = '';
      if (this.browser) {
        this.errors = padValidate.check(this.rules, this.form);
        if (Object.keys(this.errors).length) {
          this.note = 'Stopped in the browser - the same rules the server checks.';
          return;
        }
      }
      this.busy = true;
      try {
        const answer = await pad.post('examples/form', this.form);
        this.errors = Array.isArray(answer.errors) ? {} : answer.errors;
        this.people = answer.people;
        if (answer.saved) {
          this.form = { name: '', email: '', team: '', seats: '' };
          this.note = 'Booked - PAD kept it in your session.';
        } else {
          this.note = 'The server sent it back - these are its messages.';
        }
      } catch (error) {
        this.note = error.message;
      } finally {
        this.busy = false;
      }
    }
  }));

});
