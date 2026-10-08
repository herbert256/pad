// Polling: the board asks its own page for JSON every few seconds - ?examples/live&padFormat=json
// answers only $status - and stops while the tab is hidden, so a forgotten tab does not keep
// the server busy. The round trips are drawn as they come back.

function LiveBoard({ every: firstEvery }) {
  const [every, setEvery] = React.useState(firstEvery);
  const [running, setRunning] = React.useState(true);
  const [visible, setVisible] = React.useState(!document.hidden);
  const [status, setStatus] = React.useState(null);
  const [trips, setTrips] = React.useState([]);
  const [error, setError] = React.useState(null);

  React.useEffect(() => {
    const listen = () => setVisible(!document.hidden);
    document.addEventListener('visibilitychange', listen);
    return () => document.removeEventListener('visibilitychange', listen);
  }, []);

  React.useEffect(() => {
    if (!running || !visible) return;
    let gone = false;
    const ask = async () => {
      const started = performance.now();
      try {
        const answer = await PadReact.get('examples/live');
        if (gone) return;
        setStatus(answer.status);
        setTrips(now => [...now, Math.round(performance.now() - started)].slice(-40));
        setError(null);
      } catch (failure) {
        if (!gone) setError(failure.message);
      }
    };
    ask();
    const timer = setInterval(ask, every * 1000);
    return () => { gone = true; clearInterval(timer); };
  }, [running, visible, every]);

  const live = running && visible;
  const tiles = status ? [
    ['Server time', <span key={status.time} className="pop" style={{ display: 'inline-block' }}>{status.time}</span>, status.date],
    ['Asks, all visitors', <CountUp value={status.hits} />, 'kept by padCache'],
    ['Peak memory', status.memory + ' MB', 'of this request'],
    ['Load', status.load === null ? '-' : status.load, 'PHP ' + status.php + ' · pid ' + status.pid]
  ] : [];

  return (
    <div>
      <div className="toolbar">
        <span className={'badge ' + (live ? 'is-good' : 'is-bad')}>
          {live ? <><span className="dot" /> polling every {every} s</> : visible ? 'paused' : 'paused while the tab is hidden'}
        </span>
        <div className="pills" role="group" aria-label="Every" style={{ marginLeft: 'auto' }}>
          {[1, 2, 5].map(seconds => (
            <button key={seconds} className={'pill' + (every === seconds ? ' is-on' : '')} onClick={() => setEvery(seconds)}>{seconds} s</button>
          ))}
        </div>
        <button className={'btn-sm' + (running ? ' btn-ghost' : '')} onClick={() => setRunning(!running)}>{running ? '❚❚ Pause' : '▶ Resume'}</button>
      </div>

      {error && <div className="callout"><p>The last ask failed: {error}</p></div>}

      <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(190px, 1fr))' }}>
        {status === null && [0, 1, 2, 3].map(index => <div key={index} className="skeleton" style={{ height: 112 }} />)}
        {tiles.map(([label, value, note]) => (
          <div key={label} className="panel">
            <div className="panel-title">{label}</div>
            <div style={{ font: '800 28px var(--mono)' }}>{value}</div>
            <div className="muted small">{note}</div>
          </div>
        ))}
      </div>

      <div className="panel" style={{ marginTop: 18 }}>
        <div className="panel-title">Round trips, ms</div>
        <Sparkline values={trips} />
      </div>
    </div>
  );
}

function Sparkline({ values }) {
  if (values.length < 2) return <div className="muted small">Waiting for a second answer ...</div>;

  // drawn in a box of its own units and stretched to the panel's width; the strokes keep
  // their width however wide that is
  const width = 600, height = 70, top = Math.max(...values, 10);
  const step = width / (values.length - 1);
  const line = values.map((value, index) => (index ? 'L' : 'M') + (index * step).toFixed(1) + ' ' + (height - 4 - value / top * (height - 12)).toFixed(1)).join(' ');

  return (
    <div>
      <svg viewBox={`0 0 ${width} ${height}`} preserveAspectRatio="none" style={{ width: '100%', height: 70, display: 'block' }}
           role="img" aria-label={'Last round trip ' + values[values.length - 1] + ' ms'}>
        <defs>
          <linearGradient id="spark-fill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stopColor="var(--react)" stopOpacity=".35" />
            <stop offset="1" stopColor="var(--react)" stopOpacity="0" />
          </linearGradient>
        </defs>
        <path d={line + ` L ${width} ${height} L 0 ${height} Z`} fill="url(#spark-fill)" />
        <path d={line} fill="none" stroke="var(--react)" strokeWidth="2" strokeLinejoin="round" vectorEffect="non-scaling-stroke" />
      </svg>
      <div className="muted small" style={{ display: 'flex', justifyContent: 'space-between' }}>
        <span>{values.length} answers</span>
        <span>last {values[values.length - 1]} ms &middot; slowest {Math.max(...values)} ms</span>
      </div>
    </div>
  );
}

PadReact.island('LiveBoard', LiveBoard);
