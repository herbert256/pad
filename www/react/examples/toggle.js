// One Switch component, used three times: each copy has its own state. The room around them
// reads all three, held by the parent - lifting state up.

function Switch({ label, on, onChange }) {
  return (
    <button role="switch" aria-checked={on} onClick={() => onChange(!on)} className="btn-ghost"
            style={{ justifyContent: 'space-between', width: '100%', padding: '12px 16px', borderRadius: 14 }}>
      <span>{label}</span>
      <span style={{ position: 'relative', width: 46, height: 26, borderRadius: 999, background: on ? 'var(--good)' : 'var(--surface-3)', transition: 'background .2s' }}>
        <span style={{ position: 'absolute', top: 3, left: on ? 23 : 3, width: 20, height: 20, borderRadius: '50%', background: '#fff',
                       boxShadow: '0 1px 3px rgba(0,0,0,.3)', transition: 'left .2s cubic-bezier(.2,.8,.2,1)' }} />
      </span>
    </button>
  );
}

function ToggleExample() {
  const [lamp, setLamp] = React.useState(false);
  const [music, setMusic] = React.useState(false);
  const [heating, setHeating] = React.useState(true);

  return (
    <div className="cols-2">
      <div style={{ display: 'grid', gap: 10 }}>
        <Switch label="💡 Lamp" on={lamp} onChange={setLamp} />
        <Switch label="🎵 Music" on={music} onChange={setMusic} />
        <Switch label="🔥 Heating" on={heating} onChange={setHeating} />
      </div>
      <div className="panel" style={{ minHeight: 170, display: 'grid', placeItems: 'center', textAlign: 'center', transition: 'background .4s',
                                      background: lamp ? 'radial-gradient(circle at 50% 30%, rgba(255, 214, 90, .55), var(--surface-2) 70%)' : 'var(--surface-2)' }}>
        <div>
          <div style={{ fontSize: 54 }}>{lamp ? '🌞' : '🌙'}</div>
          <div className="muted small">
            {music ? 'music playing' : 'quiet'} &middot; {heating ? '21°' : '16°'} &middot; {[lamp, music, heating].filter(Boolean).length} of 3 on
          </div>
        </div>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example4')).render(<ToggleExample />);
