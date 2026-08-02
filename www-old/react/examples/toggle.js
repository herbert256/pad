function ToggleExample() {
  const [isOn, setIsOn] = React.useState(false);

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ TOGGLE SWITCH</div>
      <div style={{textAlign: 'center'}}>
        <div
          style={{
            fontSize: '48px',
            marginBottom: '20px',
            color: isOn ? '#4caf50' : '#999'
          }}
        >
          {isOn ? '💡 ON' : '💡 OFF'}
        </div>
        <button
          onClick={() => setIsOn(!isOn)}
          style={{
            background: isOn ? '#4caf50' : '#999',
            padding: '15px 30px',
            fontSize: '18px'
          }}
        >
          {isOn ? 'Turn Off' : 'Turn On'}
        </button>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example4')).render(<ToggleExample />);
