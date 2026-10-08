// The small pieces the examples share - JSX, turned into JavaScript by Babel in the browser
// like every component of this application, and loaded on every page by _inits.pad before
// the page's own component. A function declared here is global, so a later script uses it.

// Every request PadReact made on this page, newest first: method, address, status, time
// and size - what goes back and forth between the island and PAD.

function RequestLog({ title = 'Requests to PAD' }) {
  const [entries, setEntries] = React.useState(() => PadReact.requests.slice());

  React.useEffect(() => {
    const listen = () => setEntries(PadReact.requests.slice());
    window.addEventListener('pad:request', listen);
    return () => window.removeEventListener('pad:request', listen);
  }, []);

  return (
    <div className="request-log">
      <div className="request-log-head">
        <span>{title}</span>
        <span>{entries.length ? entries.length + (entries.length === 1 ? ' request' : ' requests') : ''}</span>
      </div>
      {entries.length === 0 ? (
        <div className="empty">Nothing yet - the first answer came with the page itself.</div>
      ) : (
        <ol>
          {entries.map((entry, index) => (
            <li key={entries.length - index}>
              <span className="method">{entry.method}</span>
              <span className={'status' + (entry.status >= 400 ? ' is-bad' : '')}>{entry.status}</span>
              <span className="url" title={entry.url}>
                {entry.method === 'GET' ? <a href={entry.url} target="_blank" rel="noopener">{shortUrl(entry.url)}</a> : shortUrl(entry.url)}
              </span>
              <span className="num">{entry.ms} ms</span>
              <span className="num size">{formatBytes(entry.bytes)}</span>
            </li>
          ))}
        </ol>
      )}
    </div>
  );
}

function shortUrl(url) {
  return decodeURIComponent(url.substring(url.indexOf('?')));
}

function formatBytes(bytes) {
  return bytes < 1024 ? bytes + ' B' : (bytes / 1024).toFixed(1) + ' kB';
}

function money(amount) {
  return '€ ' + Number(amount).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function Stars({ rating }) {
  const full = Math.round(rating);
  return <span className="stars" title={rating + ' out of 5'}>{'★'.repeat(full)}{'☆'.repeat(5 - full)}</span>;
}

function Avatar({ name, hue = 200 }) {
  const initials = name.split(' ').map(part => part[0]).slice(0, 2).join('');
  return (
    <span className="avatar" style={{ background: `linear-gradient(135deg, hsl(${hue} 70% 55%), hsl(${(hue + 40) % 360} 70% 45%))` }}>
      {initials}
    </span>
  );
}

// A message at the foot of the window for two seconds; useToast gives the setter and the
// element to render.

function useToast() {
  const [text, setText] = React.useState('');
  React.useEffect(() => {
    if (!text) return;
    const timer = setTimeout(() => setText(''), 2200);
    return () => clearTimeout(timer);
  }, [text]);
  return [text ? <div className="toast" role="status">{text}</div> : null, setText];
}

// A number that counts up to its value when it appears or changes.

function CountUp({ value, duration = 700 }) {
  const [shown, setShown] = React.useState(0);
  const from = React.useRef(0);

  React.useEffect(() => {
    const start = performance.now();
    const begin = from.current;
    let frame;
    const step = now => {
      const t = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - t, 3);
      setShown(Math.round(begin + (value - begin) * eased));
      if (t < 1) frame = requestAnimationFrame(step);
      else from.current = value;
    };
    frame = requestAnimationFrame(step);
    return () => cancelAnimationFrame(frame);
  }, [value]);

  return <span className="num">{shown.toLocaleString('en')}</span>;
}
