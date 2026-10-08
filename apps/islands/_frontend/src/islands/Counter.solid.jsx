import { createSignal } from 'solid-js';

export default function Counter(props) {
  const [count, setCount] = createSignal(props.start);
  return (
    <div class="counter">
      <div class="counter-value">{count()}</div>
      <div class="counter-buttons">
        <button onClick={() => setCount(count() - 1)} aria-label="One less">−</button>
        <button onClick={() => setCount(count() + 1)} aria-label="One more">+</button>
      </div>
      <p class="counter-note">{props.framework}: createSignal</p>
    </div>
  );
}
