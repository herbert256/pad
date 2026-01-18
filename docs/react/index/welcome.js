// Simple React component
function WelcomeComponent() {
  const [count, setCount] = React.useState(0);

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ REACT COMPONENT</div>
      <h3>Interactive Counter</h3>
      <p>Current count: <strong>{count}</strong></p>
      <button onClick={() => setCount(count + 1)}>
        Click to Increment
      </button>
      <button onClick={() => setCount(0)} style={{marginLeft: '10px', background: '#ff6b6b'}}>
        Reset
      </button>
    </div>
  );
}

// Render the component
const welcomeRoot = ReactDOM.createRoot(document.getElementById('welcome-component'));
welcomeRoot.render(<WelcomeComponent />);
