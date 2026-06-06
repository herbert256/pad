function CounterApp() {
  // Get initial value from server
  const initialCount = parseInt(document.getElementById('counter-app').dataset.initialCount);

  const [count, setCount] = React.useState(initialCount);
  const [step, setStep] = React.useState(1);
  const [history, setHistory] = React.useState([initialCount]);

  const increment = () => {
    const newCount = count + step;
    setCount(newCount);
    setHistory([...history, newCount]);
  };

  const decrement = () => {
    const newCount = count - step;
    setCount(newCount);
    setHistory([...history, newCount]);
  };

  const reset = () => {
    setCount(0);
    setHistory([0]);
  };

  const undo = () => {
    if (history.length > 1) {
      const newHistory = history.slice(0, -1);
      setHistory(newHistory);
      setCount(newHistory[newHistory.length - 1]);
    }
  };

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ REACT COUNTER APP</div>

      <div style={{textAlign: 'center', marginTop: '20px'}}>
        <div style={{fontSize: '72px', fontWeight: 'bold', color: '#282c34', marginBottom: '30px'}}>
          {count}
        </div>

        <div style={{marginBottom: '20px'}}>
          <label style={{marginRight: '10px'}}>
            Step size:
            <input
              type="number"
              value={step}
              onChange={(e) => setStep(parseInt(e.target.value) || 1)}
              style={{
                marginLeft: '10px',
                padding: '5px 10px',
                fontSize: '16px',
                width: '80px',
                borderRadius: '5px',
                border: '1px solid #ccc'
              }}
            />
          </label>
        </div>

        <div style={{marginBottom: '20px'}}>
          <button onClick={increment}>
            + {step}
          </button>
          <button onClick={decrement} style={{marginLeft: '10px', background: '#ff6b6b'}}>
            - {step}
          </button>
          <button onClick={reset} style={{marginLeft: '10px', background: '#ffa500'}}>
            Reset
          </button>
          <button
            onClick={undo}
            disabled={history.length <= 1}
            style={{
              marginLeft: '10px',
              background: history.length <= 1 ? '#ccc' : '#9b59b6',
              cursor: history.length <= 1 ? 'not-allowed' : 'pointer'
            }}
          >
            Undo
          </button>
        </div>

        <div style={{
          marginTop: '30px',
          padding: '15px',
          background: '#f0f0f0',
          borderRadius: '5px',
          textAlign: 'left'
        }}>
          <strong>Statistics:</strong>
          <ul style={{marginTop: '10px', listStyle: 'none'}}>
            <li>Initial value from server: {initialCount}</li>
            <li>Current value: {count}</li>
            <li>Total operations: {history.length - 1}</li>
            <li>History length: {history.length}</li>
          </ul>
        </div>

        {history.length > 1 && (
          <div style={{
            marginTop: '20px',
            padding: '15px',
            background: '#e3f2fd',
            borderRadius: '5px',
            textAlign: 'left'
          }}>
            <strong>History:</strong>
            <div style={{marginTop: '10px', maxHeight: '100px', overflow: 'auto'}}>
              {history.map((value, index) => (
                <span
                  key={index}
                  style={{
                    display: 'inline-block',
                    padding: '5px 10px',
                    margin: '2px',
                    background: index === history.length - 1 ? '#61dafb' : '#fff',
                    borderRadius: '3px',
                    fontSize: '14px'
                  }}
                >
                  {value}
                </span>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

const counterRoot = ReactDOM.createRoot(document.getElementById('counter-app'));
counterRoot.render(<CounterApp />);
