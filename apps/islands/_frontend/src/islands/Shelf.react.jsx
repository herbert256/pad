// The shelf, in React: its products and the first cart come from PAD's props. Adding posts to
// the page, which keeps the cart in the session and answers it - and the answer goes into the
// shared store, where the Vue summary and the Svelte badge read it too.
import { useSyncExternalStore, useState } from 'react';
import { store, post } from '../pad-islands.js';

export default function Shelf({ products, cart }) {
  const shared = store('cart', cart);
  const now = useSyncExternalStore(shared.subscribe, shared.get);
  const [busy, setBusy] = useState(0);

  const add = async (id) => {
    setBusy(id);
    try { shared.set((await post('examples/shared', { op: 'add', id })).cart); }
    finally { setBusy(0); }
  };

  const inCart = (id) => now.lines.find((line) => line.id === id)?.quantity || 0;

  return (
    <div className="shelf">
      {products.map((product) => (
        <div className="card item" key={product.id}>
          <span className="item-emoji">{product.emoji}</span>
          <strong>{product.name}</strong>
          <span className="muted small">€ {product.price.toFixed(2)}</span>
          <button className="btn-sm" disabled={busy === product.id} onClick={() => add(product.id)}>
            + Add {inCart(product.id) > 0 && <span className="badge">{inCart(product.id)}</span>}
          </button>
        </div>
      ))}
      <p className="framework-tag">React</p>
    </div>
  );
}
