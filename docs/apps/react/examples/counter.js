// The start value is the one PHP gave: data-initial-count, read through dataset. The count,
// the step, the history for undo and the statistics are state of this component only.

function CounterApp() {
  const initialCount = parseInt(document.getElementById('counter-app').dataset.initialCount, 10);

  const [count, setCount] = React.useState(initialCount);
  const [step, setStep] = React.useState(1);
  const [history, setHistory] = React.useState([initialCount]);

  const go = value => {
    setCount(value);
    setHistory([...history, value]);
  };

  const undo = () => {
    const back = history.slice(0, -1);
    setHistory(back);
    setCount(back[back.length - 1]);
  };

  const highest = Math.max(...history), lowest = Math.min(...history);

  return (
    <div className="cols-2">
      <div style={{ textAlign: 'center' }}>
        <div key={count} className="pop" style={{ fontSize: 96, fontWeight: 900, letterSpacing: '-.04em', lineHeight: 1.1,
             background: 'linear-gradient(100deg, var(--pad), var(--react))', WebkitBackgroundClip: 'text', backgroundClip: 'text', color: 'transparent' }}>
          {count}
        </div>

        <div style={{ display: 'flex', gap: 10, justifyContent: 'center', margin: '18px 0' }}>
          <button className="btn-danger" onClick={() => go(count - step)}>− {step}</button>
          <button onClick={() => go(count + step)}>+ {step}</button>
        </div>

        <label className="small" style={{ display: 'flex', gap: 12, alignItems: 'center', justifyContent: 'center' }}>
          step
          <input type="range" min="1" max="10" value={step} onChange={event => setStep(Number(event.target.value))} />
          <strong style={{ width: 20 }}>{step}</strong>
        </label>

        <div style={{ display: 'flex', gap: 10, justifyContent: 'center', marginTop: 18 }}>
          <button className="btn-ghost btn-sm" onClick={undo} disabled={history.length <= 1}>↶ Undo</button>
          <button className="btn-ghost btn-sm" onClick={() => go(initialCount)}>Back to {initialCount}</button>
        </div>
      </div>

      <div>
        <div className="grid" style={{ gridTemplateColumns: 'repeat(2, 1fr)', gap: 10 }}>
          {[['From PHP', initialCount], ['Changes', history.length - 1], ['Highest', highest], ['Lowest', lowest]].map(([label, value]) => (
            <div key={label} className="panel">
              <div className="panel-title">{label}</div>
              <div style={{ font: '800 24px var(--mono)' }}>{value}</div>
            </div>
          ))}
        </div>
        <div className="panel" style={{ marginTop: 10 }}>
          <div className="panel-title">History</div>
          <div className="chips">
            {history.slice(-24).map((value, index, shown) => (
              <span key={history.length - shown.length + index} className={'badge' + (index === shown.length - 1 ? ' is-react' : '')}>{value}</span>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('counter-app')).render(<CounterApp />);
