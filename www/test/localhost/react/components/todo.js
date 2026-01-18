// TodoItem Component
function TodoItem({ todo, onToggle, onDelete }) {
  return (
    <div style={{
      display: 'flex',
      alignItems: 'center',
      padding: '10px',
      background: todo.completed ? '#d4edda' : 'white',
      border: '1px solid #ddd',
      borderRadius: '5px',
      marginBottom: '5px'
    }}>
      <input
        type="checkbox"
        checked={todo.completed}
        onChange={() => onToggle(todo.id)}
        style={{marginRight: '10px'}}
      />
      <span style={{
        flex: 1,
        textDecoration: todo.completed ? 'line-through' : 'none',
        color: todo.completed ? '#666' : '#000'
      }}>
        {todo.text}
      </span>
      <button
        onClick={() => onDelete(todo.id)}
        style={{
          background: '#ff6b6b',
          color: 'white',
          border: 'none',
          padding: '5px 10px',
          borderRadius: '3px',
          cursor: 'pointer'
        }}
      >
        Delete
      </button>
    </div>
  );
}

// TodoApp Component
function TodoApp() {
  const [todos, setTodos] = React.useState([
    { id: 1, text: 'Learn React basics', completed: true },
    { id: 2, text: 'Build a component', completed: false },
    { id: 3, text: 'Integrate with PAD', completed: false }
  ]);
  const [newTodo, setNewTodo] = React.useState('');

  const addTodo = () => {
    if (newTodo.trim()) {
      setTodos([...todos, {
        id: Date.now(),
        text: newTodo,
        completed: false
      }]);
      setNewTodo('');
    }
  };

  const toggleTodo = (id) => {
    setTodos(todos.map(todo =>
      todo.id === id ? { ...todo, completed: !todo.completed } : todo
    ));
  };

  const deleteTodo = (id) => {
    setTodos(todos.filter(todo => todo.id !== id));
  };

  const completedCount = todos.filter(t => t.completed).length;

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ TODO APP (Component Composition)</div>

      <div style={{marginBottom: '20px'}}>
        <input
          type="text"
          value={newTodo}
          onChange={(e) => setNewTodo(e.target.value)}
          onKeyPress={(e) => e.key === 'Enter' && addTodo()}
          placeholder="Enter a new todo..."
          style={{
            padding: '10px',
            fontSize: '16px',
            border: '1px solid #ccc',
            borderRadius: '5px',
            width: '70%',
            marginRight: '10px'
          }}
        />
        <button onClick={addTodo}>Add Todo</button>
      </div>

      <div style={{marginBottom: '15px', color: '#666'}}>
        {completedCount} of {todos.length} completed
      </div>

      <div>
        {todos.map(todo => (
          <TodoItem
            key={todo.id}
            todo={todo}
            onToggle={toggleTodo}
            onDelete={deleteTodo}
          />
        ))}
      </div>

      {todos.length === 0 && (
        <div style={{
          padding: '20px',
          textAlign: 'center',
          color: '#999'
        }}>
          No todos yet. Add one above!
        </div>
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('todo-app')).render(<TodoApp />);
