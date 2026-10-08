// An event handler reads the click - where it landed in the element, through a ref - and
// leaves a ripple there. Each ripple is an entry of state that removes itself when its
// animation is over.

function ClickExample() {
  const area = React.useRef(null);
  const [ripples, setRipples] = React.useState([]);
  const [clicks, setClicks] = React.useState(0);

  const click = event => {
    const box = area.current.getBoundingClientRect();
    const ripple = { id: Date.now() + Math.random(), x: event.clientX - box.left, y: event.clientY - box.top, hue: Math.round(Math.random() * 360) };
    setRipples(now => [...now, ripple]);
    setClicks(clicks + 1);
  };

  const gone = id => setRipples(now => now.filter(ripple => ripple.id !== id));

  return (
    <div>
      <div ref={area} onClick={click} role="button" tabIndex={0} aria-label="Click anywhere"
           style={{ position: 'relative', height: 240, borderRadius: 16, overflow: 'hidden', cursor: 'crosshair', userSelect: 'none',
                    display: 'grid', placeItems: 'center', background: 'linear-gradient(135deg, var(--pad-soft), var(--react-soft))', border: '1px solid var(--line)' }}>
        <div style={{ textAlign: 'center', pointerEvents: 'none' }}>
          <div key={clicks} className="pop" style={{ fontSize: 56, fontWeight: 900 }}>{clicks}</div>
          <div className="muted">{clicks ? 'clicks - keep going' : 'click anywhere in here'}</div>
        </div>
        {ripples.map(ripple => (
          <span key={ripple.id} onAnimationEnd={() => gone(ripple.id)}
                style={{ position: 'absolute', left: ripple.x, top: ripple.y, width: 260, height: 260, borderRadius: '50%', pointerEvents: 'none',
                         background: `radial-gradient(circle, hsl(${ripple.hue} 90% 60% / .55), transparent 70%)`, animation: 'ripple .8s ease-out forwards' }} />
        ))}
      </div>
      <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 12 }}>
        <span className="muted small">{ripples.length} ripples on screen</span>
        <button className="btn-sm btn-ghost" onClick={() => setClicks(0)} disabled={!clicks}>Reset</button>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example1')).render(<ClickExample />);
