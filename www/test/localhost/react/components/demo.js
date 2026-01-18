// Reusable Card Component
function Card({ title, children, color = '#61dafb' }) {
  return (
    <div style={{
      border: `2px solid ${color}`,
      borderRadius: '8px',
      padding: '20px',
      margin: '10px 0',
      background: 'white'
    }}>
      {title && (
        <h3 style={{
          margin: '0 0 15px 0',
          color: color,
          borderBottom: `2px solid ${color}`,
          paddingBottom: '10px'
        }}>
          {title}
        </h3>
      )}
      {children}
    </div>
  );
}

// Reusable Button Component
function Button({ onClick, children, variant = 'primary' }) {
  const styles = {
    primary: { background: '#61dafb', color: '#282c34' },
    danger: { background: '#ff6b6b', color: 'white' },
    success: { background: '#4caf50', color: 'white' }
  };

  return (
    <button
      onClick={onClick}
      style={{
        ...styles[variant],
        border: 'none',
        padding: '10px 20px',
        borderRadius: '5px',
        fontSize: '16px',
        fontWeight: 'bold',
        cursor: 'pointer',
        margin: '5px'
      }}
    >
      {children}
    </button>
  );
}

// Main Demo Component
function ComponentsDemo() {
  const [message, setMessage] = React.useState('No button clicked yet');

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ REUSABLE COMPONENTS</div>

      <Card title="Card Component Example" color="#61dafb">
        <p>This is a reusable Card component with custom title and color.</p>
        <p>You can put any content inside it!</p>
      </Card>

      <Card title="Button Components" color="#9b59b6">
        <p>Click the buttons below to see the reusable Button components:</p>
        <div>
          <Button onClick={() => setMessage('Primary button clicked!')}>
            Primary Button
          </Button>
          <Button
            onClick={() => setMessage('Danger button clicked!')}
            variant="danger"
          >
            Danger Button
          </Button>
          <Button
            onClick={() => setMessage('Success button clicked!')}
            variant="success"
          >
            Success Button
          </Button>
        </div>
        <div style={{
          marginTop: '15px',
          padding: '10px',
          background: '#f0f0f0',
          borderRadius: '5px'
        }}>
          Message: <strong>{message}</strong>
        </div>
      </Card>

      <Card title="Nested Cards" color="#ffa500">
        <p>Cards can be nested inside each other:</p>
        <Card title="Inner Card" color="#4caf50">
          <p>This is a card inside another card!</p>
        </Card>
      </Card>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('components-demo')).render(<ComponentsDemo />);
