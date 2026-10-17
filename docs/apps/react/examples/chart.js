// React holds the choices, PAD draws. The island arrived with the first chart already in it
// - PAD rendered the {fragment 'chart'} inside, so the chart was there before any script -
// and the component keeps that HTML as its starting picture. Each change asks the page for
// that one fragment again (&padFragment=chart) and puts the SVG it answers in place.

const chartKinds = [['bar', 'Bars'], ['stacked', 'Stacked'], ['line', 'Lines'], ['hbar', 'Sideways']];
const chartChannels = ['online', 'shop', 'wholesale'];

function ChartStudio({ kind: firstKind, months: firstMonths, channels: firstChannels, serverHtml }) {
  const [kind, setKind] = React.useState(firstKind);
  const [months, setMonths] = React.useState(firstMonths);
  const [channels, setChannels] = React.useState(firstChannels);
  const [html, setHtml] = React.useState(serverHtml);
  const [busy, setBusy] = React.useState(false);
  const first = React.useRef(true);

  // the choices as one text, so the debounce compares values - an object is new each render
  const asked = PadReact.useDebounced(JSON.stringify({ kind, months, channels: channels.join(',') }), 120);

  React.useEffect(() => {
    if (first.current) { first.current = false; return; }
    const controller = new AbortController();
    setBusy(true);
    PadReact.fragment('examples/chart', 'chart', JSON.parse(asked), { signal: controller.signal })
      .then(answer => { setHtml(answer); setBusy(false); })
      .catch(error => { if (error.name !== 'AbortError') setBusy(false); });
    return () => controller.abort();
  }, [asked]);

  const flip = channel => setChannels(now =>
    now.includes(channel) ? (now.length > 1 ? now.filter(one => one !== channel) : now)
                          : chartChannels.filter(one => one === channel || now.includes(one)));

  const total = (html.match(/data-total="(\d+)"/) || [])[1];

  return (
    <div>
      <div className="toolbar">
        <div className="pills" role="group" aria-label="Kind">
          {chartKinds.map(([key, label]) => (
            <button key={key} className={'pill' + (kind === key ? ' is-on' : '')} onClick={() => setKind(key)}>{label}</button>
          ))}
        </div>
        <div className="pills" role="group" aria-label="Channels" style={{ marginLeft: 'auto' }}>
          {chartChannels.map(channel => (
            <button key={channel} className={'pill' + (channels.includes(channel) ? ' is-on' : '')} aria-pressed={channels.includes(channel)}
                    onClick={() => flip(channel)}>{channel}</button>
          ))}
        </div>
      </div>

      <label className="small" style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 14 }}>
        <span style={{ whiteSpace: 'nowrap' }}>Last {months} months</span>
        <input type="range" min="3" max="12" value={months} onChange={event => setMonths(Number(event.target.value))} style={{ flex: 1 }} />
        {busy ? <span className="spinner" /> : <span className="badge is-pad">{total ? total + 'k in total' : ''}</span>}
      </label>

      <div className="chart-frame" style={{ opacity: busy ? .5 : 1, transition: 'opacity .2s' }} dangerouslySetInnerHTML={{ __html: html }} />

      <RequestLog title="Fragments from PAD" />
    </div>
  );
}

PadReact.island('ChartStudio', ChartStudio);
