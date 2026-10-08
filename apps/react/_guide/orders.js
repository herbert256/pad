// www/react/orders.js - the props are the JSON PAD wrote; a list arrives as data

function OrderBoard({ data }) {
  const [orders, setOrders] = React.useState(data);
  const [status, setStatus] = React.useState('all');

  const shown = orders.filter(order => status === 'all' || order.status === status);

  return (
    <div>
      <select value={status} onChange={event => setStatus(event.target.value)}>
        <option value="all">All</option>
        <option value="open">Open</option>
      </select>
      {shown.map(order => <p key={order.id}>{order.customer} - {order.total}</p>)}
    </div>
  );
}

PadReact.island('OrderBoard', OrderBoard);
