// The order card, in TypeScript: its props are the order the page's PHP made, and their type
// is the one pad types wrote from the page's own data - src/types/pad.d.ts. A field the PHP
// does not have, or one used as the wrong type, fails npm run check before it fails a visitor.

import { useState } from 'react';
import type { ExamplesTypedVars } from '../types/pad';

type Order = ExamplesTypedVars['order'];
type Line = Order['lines'][number];

const money = (amount: number) => '€ ' + amount.toFixed(2);

export default function OrderCard(order: Order) {
  const [open, setOpen] = useState<Line | null>(null);
  const total = order.lines.reduce((sum, line) => sum + line.quantity * line.price, 0);

  return (
    <div className="card order">
      <div className="framework-head">
        <strong>Order #{order.number}</strong>
        <span className={'badge' + (order.paid ? ' is-good' : '')}>{order.status}{order.paid ? ' · paid' : ''}</span>
      </div>
      <p className="muted small">{order.customer.name}, {order.customer.city}</p>
      <ul className="order-lines">
        {order.lines.map((line) => (
          <li key={line.product} onClick={() => setOpen(open === line ? null : line)}>
            <span>{line.quantity} × {line.product}{line.gift ? ' 🎁' : ''}</span>
            <span>{money(line.quantity * line.price)}</span>
          </li>
        ))}
      </ul>
      <div className="order-total"><span>Total</span><strong>{money(total)}</strong></div>
      {order.note === null && <p className="muted small">No note: the order had none, so its type is null - capture an order with one, and it becomes string.</p>}
      <p className="framework-tag">React · TypeScript</p>
    </div>
  );
}
