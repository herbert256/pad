// The catalogue search. The first result came with the page, in the props; every change of
// the query, the category, the order or the page asks the same PAD page again for JSON -
// PadReact.useData aborts the request before it, so a fast typist never sees an old answer
// arrive last. The request log under it shows each round trip.

function CatalogSearch({ result: first, categories }) {
  const [query, setQuery] = React.useState('');
  const [category, setCategory] = React.useState('');
  const [sort, setSort] = React.useState('name');
  const [page, setPage] = React.useState(1);

  const q = PadReact.useDebounced(query.trim(), 250);
  const { data, loading, error } = PadReact.useData('examples/search', { q, category, sort, p: page }, { result: first });
  const result = data.result;

  // a new search starts on its first page
  const change = set => value => { set(value); setPage(1); };

  return (
    <div>
      <div className="toolbar">
        <div className="search">
          <input className="input" value={query} onChange={event => change(setQuery)(event.target.value)}
                 placeholder="Search the catalogue - try 'light' or 'usb'" aria-label="Search the catalogue" />
        </div>
        <select className="select" value={sort} onChange={event => change(setSort)(event.target.value)} aria-label="Order">
          <option value="name">Name</option>
          <option value="cheap">Price, low first</option>
          <option value="dear">Price, high first</option>
          <option value="rating">Rating</option>
        </select>
        {loading && <span className="spinner" aria-label="Asking PAD" />}
      </div>

      <div className="pills" style={{ marginBottom: 18 }}>
        {['', ...categories].map(name => (
          <button key={name || 'all'} className={'pill' + (name === category ? ' is-on' : '')} onClick={() => change(setCategory)(name)}>
            {name || 'All'}
          </button>
        ))}
      </div>

      {error && <div className="callout"><p>PAD could not answer: {error.message}</p></div>}

      <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(230px, 1fr))', opacity: loading ? .55 : 1, transition: 'opacity .2s' }}>
        {result.items.map(item => (
          <div key={item.id} className="card rise" style={{ padding: 16, display: 'flex', gap: 14, alignItems: 'center' }}>
            <span style={{ fontSize: 34 }}>{item.emoji}</span>
            <div style={{ minWidth: 0 }}>
              <strong>{item.name}</strong>
              <div className="muted small">{item.category} &middot; <Stars rating={item.rating} /></div>
              <div style={{ fontWeight: 700, marginTop: 2 }}>{money(item.price)}</div>
            </div>
          </div>
        ))}
      </div>

      {result.total === 0 && <div className="empty-state"><span className="big">🔎</span>Nothing in the catalogue matches.</div>}

      <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginTop: 18, flexWrap: 'wrap' }}>
        <button className="btn-sm btn-ghost" disabled={result.page <= 1} onClick={() => setPage(result.page - 1)}>← Previous</button>
        <span className="muted small">page {result.page} of {result.pages} &middot; {result.total} found</span>
        <button className="btn-sm btn-ghost" disabled={result.page >= result.pages} onClick={() => setPage(result.page + 1)}>Next →</button>
        <a className="small" style={{ marginLeft: 'auto' }} target="_blank" rel="noopener"
           href={PadReact.url('examples/search', { q, category, sort, p: page, padFormat: 'json' })}>Open this answer as JSON ↗</a>
      </div>

      <RequestLog />
    </div>
  );
}

PadReact.island('CatalogSearch', CatalogSearch);
