// Composition: small components that take props and children, put together into bigger
// ones. Card and Button are used several times with different props; TodoApp is built from
// TodoItem - the list's state lives in the parent, the items only report what happened.

function Card({ title, tone = 'react', children }) {
  return (
    <div className="card" style={{ borderTop: `3px solid var(--${tone})` }}>
      {title && <h3 style={{ marginTop: 0, color: `var(--${tone})` }}>{title}</h3>}
      {children}
    </div>
  );
}

function Button({ variant = '', onClick, children }) {
  return <button className={'btn-sm ' + variant} onClick={onClick}>{children}</button>;
}

function TodoItem({ todo, onToggle, onDelete }) {
  return (
    <li className="rise" style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '8px 0', borderBottom: '1px solid var(--line)' }}>
      <input type="checkbox" checked={todo.done} onChange={() => onToggle(todo.id)} aria-label={todo.text} />
      <span style={{ flex: 1, textDecoration: todo.done ? 'line-through' : 'none', color: todo.done ? 'var(--faint)' : 'inherit' }}>{todo.text}</span>
      <button className="btn-icon btn-ghost" onClick={() => onDelete(todo.id)} aria-label={'Delete ' + todo.text}>×</button>
    </li>
  );
}

function TodoApp() {
  const [todos, setTodos] = React.useState([
    { id: 1, text: 'Learn React basics', done: true },
    { id: 2, text: 'Build a component', done: false },
    { id: 3, text: 'Mount it on a PAD page', done: false }
  ]);
  const [text, setText] = React.useState('');

  const add = event => {
    event.preventDefault();
    if (!text.trim()) return;
    setTodos([...todos, { id: Date.now(), text: text.trim(), done: false }]);
    setText('');
  };

  const done = todos.filter(todo => todo.done).length;

  return (
    <div>
      <form onSubmit={add} style={{ display: 'flex', gap: 8 }}>
        <input className="input" value={text} onChange={event => setText(event.target.value)} placeholder="Something to do ..." aria-label="New todo" />
        <button type="submit">Add</button>
      </form>
      <div className="bar" style={{ margin: '14px 0 4px' }}><span style={{ width: todos.length ? `${done / todos.length * 100}%` : 0 }} /></div>
      <div className="muted small">{done} of {todos.length} done</div>
      <ul style={{ listStyle: 'none', padding: 0, margin: '8px 0 0' }}>
        {todos.map(todo => (
          <TodoItem key={todo.id} todo={todo}
                    onToggle={id => setTodos(todos.map(one => one.id === id ? { ...one, done: !one.done } : one))}
                    onDelete={id => setTodos(todos.filter(one => one.id !== id))} />
        ))}
      </ul>
      {todos.length === 0 && <div className="empty-state"><span className="big">🎉</span>All done.</div>}
    </div>
  );
}

function ComponentsDemo() {
  const [message, setMessage] = React.useState('No button pressed yet');

  return (
    <div className="cols-2">
      <div style={{ display: 'grid', gap: 16 }}>
        <Card title="A card">
          <p style={{ margin: 0 }}>Its title is a prop, this text is its <code>children</code>.</p>
        </Card>
        <Card title="Buttons, three variants" tone="pad">
          <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
            <Button onClick={() => setMessage('The primary one')}>Primary</Button>
            <Button variant="btn-good" onClick={() => setMessage('The good one')}>Good</Button>
            <Button variant="btn-danger" onClick={() => setMessage('The dangerous one')}>Danger</Button>
          </div>
          <p className="muted small" style={{ margin: '12px 0 0' }}>Pressed: <strong>{message}</strong></p>
        </Card>
        <Card title="Cards in cards" tone="good">
          <Card title="Inner" tone="warn"><p style={{ margin: 0 }}>Composition all the way down.</p></Card>
        </Card>
      </div>
      <Card title="A todo list">
        <TodoApp />
      </Card>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('components-demo')).render(<ComponentsDemo />);
