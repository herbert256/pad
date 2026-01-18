function ClickExample() {
  const handleClick = () => {
    alert('Button clicked! React is working!');
  };

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ CLICK HANDLER</div>
      <button onClick={handleClick}>Click Me!</button>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example1')).render(<ClickExample />);
