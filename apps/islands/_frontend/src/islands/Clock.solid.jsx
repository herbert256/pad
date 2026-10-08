// A clock in Solid, started from the server's time: only the text node changes each second.
import { createSignal, onCleanup } from 'solid-js';

export default function Clock(props) {
  const offset = props.now * 1000 - Date.now();
  const [now, setNow] = createSignal(new Date(Date.now() + offset));
  const timer = setInterval(() => setNow(new Date(Date.now() + offset)), 1000);
  onCleanup(() => clearInterval(timer));
  return <span class="clock">{now().toLocaleTimeString()}</span>;
}
