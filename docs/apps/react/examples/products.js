// Bare React, no runtime: the component finds its element, reads the JSON the {json} tag
// wrote into data-products (_tags/json.php reads _data/products.json), and renders a
// product grid that filters and sorts in the browser.

function ProductGrid() {
  const products = React.useMemo(() => JSON.parse(document.getElementById('products').dataset.products), []);
  const categories = ['All', ...new Set(products.map(product => product.category))];

  const [category, setCategory] = React.useState('All');
  const [sort, setSort] = React.useState('name');
  const [inStock, setInStock] = React.useState(false);

  const shown = products
    .filter(product => category === 'All' || product.category === category)
    .filter(product => !inStock || product.stock > 0)
    .sort({
      name:   (a, b) => a.name.localeCompare(b.name),
      cheap:  (a, b) => a.price - b.price,
      dear:   (a, b) => b.price - a.price,
      rating: (a, b) => b.rating - a.rating
    }[sort]);

  return (
    <div>
      <div className="toolbar">
        <div className="pills" role="group" aria-label="Category">
          {categories.map(name => (
            <button key={name} className={'pill' + (name === category ? ' is-on' : '')} onClick={() => setCategory(name)}>{name}</button>
          ))}
        </div>
        <label className="small" style={{ display: 'flex', gap: 6, alignItems: 'center', marginLeft: 'auto' }}>
          <input type="checkbox" checked={inStock} onChange={event => setInStock(event.target.checked)} /> in stock
        </label>
        <select className="select" value={sort} onChange={event => setSort(event.target.value)} aria-label="Sort">
          <option value="name">Name</option>
          <option value="cheap">Price, low first</option>
          <option value="dear">Price, high first</option>
          <option value="rating">Rating</option>
        </select>
      </div>

      <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(210px, 1fr))' }}>
        {shown.map(product => <ProductTile key={product.id} product={product} />)}
      </div>
      <p className="muted small" style={{ marginTop: 14 }}>{shown.length} of {products.length} products</p>
    </div>
  );
}

function ProductTile({ product }) {
  return (
    <div className="card rise" style={{ padding: 18 }}>
      <div style={{ fontSize: 40, lineHeight: 1 }}>{product.emoji}</div>
      <h3 style={{ fontSize: 16, margin: '12px 0 2px' }}>{product.name}</h3>
      <div className="muted small" style={{ minHeight: 44 }}>{product.blurb}</div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 10 }}>
        <strong>{money(product.price)}</strong>
        <Stars rating={product.rating} />
      </div>
      <div style={{ marginTop: 8 }}>
        {product.stock > 0
          ? <span className="badge is-good">{product.stock} in stock</span>
          : <span className="badge is-bad">sold out</span>}
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('products')).render(<ProductGrid />);
