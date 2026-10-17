// Two islands, two React roots, one cart - kept by PAD in the session. Whichever island
// changes it posts to the page and gets the whole cart back, then tells the other one with a
// 'pad:cart' event on window. Neither keeps a cart of its own that could drift: the server's
// answer is the cart. Reload the page and both start from the session again.

function cartChange(op, id) {
  return PadReact.post('examples/cart', id === undefined ? { op } : { op, id }).then(answer => {
    window.dispatchEvent(new CustomEvent('pad:cart', { detail: answer.cart }));
    return answer.cart;
  });
}

function useCart(first) {
  const [cart, setCart] = React.useState(first);
  React.useEffect(() => {
    const listen = event => setCart(event.detail);
    window.addEventListener('pad:cart', listen);
    return () => window.removeEventListener('pad:cart', listen);
  }, []);
  return cart;
}

function Shelf({ products, cart: first }) {
  const cart = useCart(first);
  const [busy, setBusy] = React.useState(null);
  const [toast, say] = useToast();

  const inCart = id => (cart.lines.find(line => line.id === id) || {}).quantity || 0;

  const add = async product => {
    setBusy(product.id);
    try {
      const after = await cartChange('add', product.id);
      const line = after.lines.find(one => one.id === product.id);
      say(line && line.quantity > inCart(product.id) ? `${product.emoji} ${product.name} added` : `No more ${product.name} in stock`);
    } catch (error) {
      say(error.message);
    } finally {
      setBusy(null);
    }
  };

  return (
    <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))' }}>
      {products.map(product => (
        <div key={product.id} className="card" style={{ padding: 16 }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start' }}>
            <span style={{ fontSize: 34 }}>{product.emoji}</span>
            {inCart(product.id) > 0 && <span key={inCart(product.id)} className="badge is-react pop">{inCart(product.id)} in cart</span>}
          </div>
          <strong style={{ display: 'block', marginTop: 8 }}>{product.name}</strong>
          <div className="muted small">{money(product.price)} &middot; {product.stock ? product.stock + ' in stock' : 'sold out'}</div>
          <button className="btn-sm" style={{ marginTop: 10, width: '100%' }} disabled={busy === product.id} onClick={() => add(product)}>
            {busy === product.id ? <span className="spinner" /> : '+'} Add
          </button>
        </div>
      ))}
      {toast}
    </div>
  );
}

function Basket(props) {
  const cart = useCart(props);
  const [busy, setBusy] = React.useState(false);

  const change = async (op, id) => {
    setBusy(true);
    try { await cartChange(op, id); } finally { setBusy(false); }
  };

  return (
    <div className="card" style={{ position: 'sticky', top: 80 }}>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h3 style={{ margin: 0 }}>🛒 Cart</h3>
        <span key={cart.count} className="badge is-react pop">{cart.count} {cart.count === 1 ? 'item' : 'items'}</span>
      </div>

      {cart.lines.length === 0 ? (
        <div className="empty-state"><span className="big">🧺</span>Empty - add something from the shelf.</div>
      ) : (
        <div style={{ margin: '14px 0', display: 'grid', gap: 10 }}>
          {cart.lines.map(line => (
            <div key={line.id} style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
              <span style={{ fontSize: 22 }}>{line.emoji}</span>
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ fontWeight: 600, fontSize: 14 }}>{line.name}</div>
                <div className="muted small">{line.quantity} × {money(line.price)}</div>
              </div>
              <button className="btn-icon btn-ghost" disabled={busy} aria-label={'One ' + line.name + ' less'} onClick={() => change('remove', line.id)}>−</button>
              <button className="btn-icon btn-ghost" disabled={busy} aria-label={'One ' + line.name + ' more'} onClick={() => change('add', line.id)}>+</button>
            </div>
          ))}
        </div>
      )}

      <div style={{ display: 'flex', justifyContent: 'space-between', borderTop: '1px solid var(--line)', paddingTop: 12, fontWeight: 800 }}>
        <span>Total</span><span>{money(cart.total)}</span>
      </div>
      {cart.lines.length > 0 && (
        <button className="btn-sm btn-ghost" style={{ marginTop: 12 }} disabled={busy} onClick={() => change('clear')}>Empty the cart</button>
      )}
      <RequestLog title="Posts to PAD" />
    </div>
  );
}

PadReact.island('Shelf', Shelf);
PadReact.island('Basket', Basket);
