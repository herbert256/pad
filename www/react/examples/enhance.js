// The island arrives with a complete table inside - PAD rendered it, and without JavaScript
// that table is the page. The component gets the same rows as props.data (a list in
// data-props) and what PAD rendered as props.serverHtml, and draws the table again with a
// search box and sortable columns.

const enhanceColumns = [
  ['name', 'Name'], ['role', 'Role'], ['team', 'Team'], ['city', 'City'], ['commits', 'Commits']
];

function TeamTable({ data, serverHtml }) {
  const [query, setQuery] = React.useState('');
  const [sort, setSort] = React.useState({ key: 'name', up: true });
  const [raw, setRaw] = React.useState(false);

  const rows = React.useMemo(() => {
    const words = query.toLowerCase().split(/\s+/).filter(Boolean);
    const found = data.filter(row => {
      const text = Object.values(row).join(' ').toLowerCase();
      return words.every(word => text.includes(word));
    });
    return found.sort((a, b) => {
      const order = typeof a[sort.key] === 'number' ? a[sort.key] - b[sort.key] : String(a[sort.key]).localeCompare(b[sort.key]);
      return sort.up ? order : -order;
    });
  }, [data, query, sort]);

  const sortBy = key => setSort(now => ({ key, up: now.key === key ? !now.up : true }));

  if (raw)
    return (
      <div>
        <div className="toolbar">
          <span className="badge is-pad">What PAD sent - plain HTML, no script needed</span>
          <button className="btn-sm btn-ghost" onClick={() => setRaw(false)}>Back to React</button>
        </div>
        <div dangerouslySetInnerHTML={{ __html: serverHtml }} />
      </div>
    );

  return (
    <div>
      <div className="toolbar">
        <div className="search">
          <input className="input" value={query} onChange={event => setQuery(event.target.value)}
                 placeholder="Search name, role, city, skill ..." aria-label="Search the team" />
        </div>
        <span className="badge is-react">⚛ enhanced</span>
        <button className="btn-sm btn-ghost" onClick={() => setRaw(true)}>Show what PAD sent</button>
      </div>

      <table className="table">
        <thead>
          <tr>
            {enhanceColumns.map(([key, label]) => (
              <th key={key} className={key === 'commits' ? 'num' : ''} aria-sort={sort.key === key ? (sort.up ? 'ascending' : 'descending') : 'none'}>
                <button onClick={() => sortBy(key)}>{label} {sort.key === key ? (sort.up ? '▲' : '▼') : ''}</button>
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.map(row => (
            <tr key={row.id}>
              <td>
                <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                  <Avatar name={row.name} hue={row.hue} />
                  <Highlight text={row.name} query={query} />
                </div>
              </td>
              <td><Highlight text={row.role} query={query} /></td>
              <td><span className="badge is-pad">{row.team}</span></td>
              <td><Highlight text={row.city} query={query} /></td>
              <td className="num">{row.commits.toLocaleString('en')}</td>
            </tr>
          ))}
        </tbody>
      </table>
      {rows.length === 0 && <div className="empty-state"><span className="big">🔍</span>Nobody matches "{query}".</div>}
      <p className="muted small" style={{ marginTop: 12 }}>{rows.length} of {data.length} shown - sorting and searching never leave the browser.</p>
    </div>
  );
}

// The text with every search word marked.

function Highlight({ text, query }) {
  const words = query.trim().split(/\s+/).filter(Boolean).map(word => word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
  if (!words.length) return text;
  const parts = text.split(new RegExp('(' + words.join('|') + ')', 'gi'));
  return parts.map((part, index) => index % 2 ? <mark key={index}>{part}</mark> : part);
}

PadReact.island('TeamTable', TeamTable);
