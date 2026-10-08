// A form React holds and PAD checks. The rules are the server's, in feedback.php - and the
// component has the same list in its props (padValidateClient), which the checker {validator}
// brought judges in the browser first: a field when it is left, the whole form before it is
// posted, with the messages the server would give. Switch that off and the post goes as it
// is - the server's answer names the field that broke a rule. The post carries the session's
// CSRF token in a header; the second button leaves it out, and PAD refuses the post before
// the page's PHP ever runs.

const feedbackMoods = { happy: '😊', curious: '🤔', puzzled: '😵‍💫' };
const feedbackEmpty = { name: '', email: '', mood: 'happy', message: '' };

function FeedbackWall({ wall: first, rules }) {
  const [form, setForm] = React.useState(feedbackEmpty);
  const [errors, setErrors] = React.useState({});
  const [wall, setWall] = React.useState(first);
  const [busy, setBusy] = React.useState(false);
  const [browser, setBrowser] = React.useState(true);
  const [toast, say] = useToast();

  // a field left: the browser's verdict on it, in the server's words
  const leave = name => () => {
    if (browser && form[name] !== '')
      setErrors(now => ({ ...now, [name]: padValidate.field(rules[name], form[name], form) || undefined }));
  };

  // an edited field drops its message - the next answer of the server says whether it holds
  const set = name => event => {
    setForm({ ...form, [name]: event.target.value });
    setErrors({ ...errors, [name]: undefined });
  };

  const send = async token => {
    if (browser && token) {
      const found = padValidate.check(rules, form);
      setErrors(found);
      if (Object.keys(found).length) {
        say('The browser stopped it - the same rules the server checks');
        return;
      }
    }
    setBusy(true);
    try {
      const answer = await PadReact.post('examples/feedback', form, { token });
      setErrors(answer.errors || {});
      setWall(answer.wall);
      if (answer.saved) {
        setForm(feedbackEmpty);
        say('Kept in your session - reload the page and it is still there');
      }
    } catch (error) {
      say(error.status === 403 ? '403 - PAD refused a post without the token' : error.message);
    } finally {
      setBusy(false);
    }
  };

  const field = (name, label, input) => (
    <div className="field">
      <label htmlFor={'feedback-' + name}>{label}</label>
      {input}
      {errors[name] && <span className="error" role="alert">{errors[name]}</span>}
    </div>
  );

  const left = 280 - form.message.length;

  return (
    <div className="cols-2">
      <form noValidate onSubmit={event => { event.preventDefault(); send(true); }}>
        {field('name', 'Name',
          <input id="feedback-name" className={'input' + (errors.name ? ' is-invalid' : '')} value={form.name} onChange={set('name')} onBlur={leave('name')} />)}
        {field('email', 'E-mail',
          <input id="feedback-email" className={'input' + (errors.email ? ' is-invalid' : '')} value={form.email} onChange={set('email')} onBlur={leave('email')} />)}

        <div className="field">
          <span className="label">Mood</span>
          <div className="pills" role="radiogroup" aria-label="Mood">
            {Object.entries(feedbackMoods).map(([mood, face]) => (
              <button type="button" key={mood} role="radio" aria-checked={form.mood === mood}
                      className={'pill' + (form.mood === mood ? ' is-on' : '')} onClick={() => setForm({ ...form, mood })}>
                {face} {mood}
              </button>
            ))}
          </div>
          {errors.mood && <span className="error">{errors.mood}</span>}
        </div>

        {field('message', 'Message',
          <textarea id="feedback-message" className={'input' + (errors.message ? ' is-invalid' : '')} value={form.message} onChange={set('message')} onBlur={leave('message')} />)}
        <div className="muted small" style={{ marginTop: -8, marginBottom: 14, color: left < 0 ? 'var(--bad)' : undefined }}>
          {left} characters left - the server says at least 10, at most 280
        </div>

        <label className="small" style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 14 }}>
          <input type="checkbox" checked={browser} onChange={event => setBrowser(event.target.checked)} />
          Check in the browser first - with the rules the server checks
        </label>

        <div style={{ display: 'flex', gap: 10, flexWrap: 'wrap' }}>
          <button type="submit" disabled={busy}>{busy ? <span className="spinner" /> : '✉️'} Post it</button>
          <button type="button" className="btn-ghost" disabled={busy} onClick={() => send(false)}>Post without the token</button>
        </div>

        <RequestLog />
      </form>

      <div>
        <div className="panel-title">Your wall - {wall.length} {wall.length === 1 ? 'message' : 'messages'}</div>
        {wall.length === 0 && (
          <div className="empty-state panel"><span className="big">🗒️</span>Empty. Post something - or post nothing, and read what PAD says.</div>
        )}
        <div style={{ display: 'grid', gap: 10 }}>
          {wall.map((note, index) => (
            <div key={wall.length - index} className="card rise" style={{ padding: 16 }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', gap: 8 }}>
                <strong>{feedbackMoods[note.mood]} {note.name}</strong>
                <span className="muted small">{note.at}</span>
              </div>
              <p style={{ margin: '6px 0 0' }}>{note.message}</p>
            </div>
          ))}
        </div>
      </div>
      {toast}
    </div>
  );
}

PadReact.island('FeedbackWall', FeedbackWall);
