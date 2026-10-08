import { useState } from 'react';

export default function Counter({ start, framework }) {
  const [count, setCount] = useState(start);
  return (
    <div className="counter">
      <div className="counter-value" key={count}>{count}</div>
      <div className="counter-buttons">
        <button onClick={() => setCount(count - 1)} aria-label="One less">−</button>
        <button onClick={() => setCount(count + 1)} aria-label="One more">+</button>
      </div>
      <p className="counter-note">{framework}: useState</p>
    </div>
  );
}
