// The island at the top of the home page. Its first props came with the page - PAD wrote
// $hello into data-props - and the button asks the same page again for the same value as
// JSON: PadReact.get('index') is ?index&padFormat=json.

function HelloServer({ message, php, time, patterns }) {
  const [hello, setHello] = React.useState({ message, php, time, patterns });
  const [asks, setAsks] = React.useState([]);
  const [busy, setBusy] = React.useState(false);

  const ask = async () => {
    setBusy(true);
    const started = performance.now();
    try {
      const answer = await PadReact.get('index');
      setHello(answer.hello);
      setAsks(now => [Math.round(performance.now() - started), ...now].slice(0, 12));
    } finally {
      setBusy(false);
    }
  };

  const slowest = Math.max(1, ...asks);

  return (
    <div className="card hello">
      <div className="panel-title">From PAD, at render time</div>
      <div style={{ display: 'flex', alignItems: 'center', gap: 14, marginBottom: 14 }}>
        <span className="avatar" style={{ background: 'linear-gradient(135deg, var(--pad), var(--react))' }}>⚛</span>
        <div>
          <div style={{ fontSize: 20, fontWeight: 800 }}>{hello.message}</div>
          <div className="muted small">PHP {hello.php} &middot; {hello.patterns} patterns to explore</div>
        </div>
      </div>

      <div className="panel" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
        <div>
          <div className="muted small">The server's clock said</div>
          <div key={hello.time} className="pop" style={{ font: '700 28px var(--mono)' }}>{hello.time}</div>
        </div>
        <button onClick={ask} disabled={busy}>{busy ? <span className="spinner" /> : '↻'} Ask again</button>
      </div>

      <div style={{ display: 'flex', alignItems: 'flex-end', gap: 4, height: 46, marginTop: 14 }} aria-label="Round trips">
        {asks.length === 0 && <span className="muted small">Each ask is a round trip to PAD - its time shows up here.</span>}
        {asks.slice().reverse().map((ms, index) => (
          <span key={index} title={ms + ' ms'} style={{ flex: 1, height: `${Math.max(8, ms / slowest * 100)}%`, borderRadius: 4,
                 background: 'linear-gradient(180deg, var(--react), var(--pad))', opacity: .35 + index / 18 }} />
        ))}
      </div>
      {asks.length > 0 && <div className="muted small" style={{ marginTop: 6 }}>last round trip {asks[0]} ms - <code>GET ?index&amp;padFormat=json</code></div>}
    </div>
  );
}

PadReact.island('HelloServer', HelloServer);
