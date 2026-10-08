// A controlled form: each field's value is state, each keystroke an update, and the checks
// run as you type - entirely in the browser. Nothing is sent anywhere; the form of
// "Forms checked by PAD" is the one the server checks.

const formRules = {
  name:  value => value.trim().length >= 2 || 'At least two characters',
  email: value => /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value) || 'That is no e-mail address',
  plan:  value => value !== '' || 'Pick a plan'
};

function FormExample() {
  const [values, setValues] = React.useState({ name: '', email: '', plan: '', news: true });
  const [touched, setTouched] = React.useState({});
  const [sent, setSent] = React.useState(null);

  const problems = Object.fromEntries(Object.entries(formRules).map(([name, rule]) => [name, rule(values[name])]).filter(([, ok]) => ok !== true));
  const valid = Object.keys(problems).length === 0;

  const set = name => event => setValues({ ...values, [name]: event.target.type === 'checkbox' ? event.target.checked : event.target.value });
  const leave = name => () => setTouched({ ...touched, [name]: true });
  const state = name => touched[name] ? (problems[name] ? ' is-invalid' : ' is-valid') : '';

  if (sent)
    return (
      <div className="empty-state rise">
        <span className="big">✅</span>
        <strong>Thanks, {sent.name}!</strong>
        <p className="muted">{sent.email} &middot; {sent.plan} plan &middot; {sent.news ? 'with' : 'without'} the newsletter</p>
        <button className="btn-ghost" onClick={() => { setSent(null); setTouched({}); }}>Fill it in again</button>
      </div>
    );

  return (
    <div className="cols-2">
      <form noValidate onSubmit={event => { event.preventDefault(); setTouched({ name: true, email: true, plan: true }); if (valid) setSent(values); }}>
        <div className="field">
          <label htmlFor="form-name">Name</label>
          <input id="form-name" className={'input' + state('name')} value={values.name} onChange={set('name')} onBlur={leave('name')} />
          {touched.name && problems.name && <span className="error">{problems.name}</span>}
        </div>
        <div className="field">
          <label htmlFor="form-email">E-mail</label>
          <input id="form-email" className={'input' + state('email')} value={values.email} onChange={set('email')} onBlur={leave('email')} />
          {touched.email && problems.email && <span className="error">{problems.email}</span>}
        </div>
        <div className="field">
          <label htmlFor="form-plan">Plan</label>
          <select id="form-plan" className={'select input' + state('plan')} value={values.plan} onChange={set('plan')} onBlur={leave('plan')}>
            <option value="">Choose ...</option>
            <option value="free">Free</option>
            <option value="team">Team</option>
            <option value="enterprise">Enterprise</option>
          </select>
          {touched.plan && problems.plan && <span className="error">{problems.plan}</span>}
        </div>
        <label className="small" style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 16 }}>
          <input type="checkbox" checked={values.news} onChange={set('news')} /> Send me the newsletter
        </label>
        <button type="submit">Sign up</button>
      </form>

      <div className="panel">
        <div className="panel-title">The state, as you type</div>
        <pre style={{ margin: 0, whiteSpace: 'pre-wrap' }}>{JSON.stringify(values, null, 2)}</pre>
        <div className="panel-title" style={{ marginTop: 14 }}>Valid</div>
        <span className={'badge ' + (valid ? 'is-good' : 'is-bad')}>{valid ? 'yes' : Object.keys(problems).join(', ') + ' not yet'}</span>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example2')).render(<FormExample />);
