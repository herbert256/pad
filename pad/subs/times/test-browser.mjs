// Exercise the actual emitted controller with a deterministic clock and small DOM harness.
import {readFileSync} from 'node:fs';
import {runInNewContext} from 'node:vm';
import assert from 'node:assert/strict';
const php = readFileSync(new URL('./_lib/time.php', import.meta.url), 'utf8');
const source = php.match(/<<<'SCRIPT'\n([\s\S]*?)\nSCRIPT/)[1];
let monotonic = 0;
let wall = new Date('2026-10-25T00:30:00Z');
let timer;
const listeners = {};
function stopwatch(total, running = false) {
  const nodes = {output: {textContent: ''}, '[data-pad-time-status]': {textContent: ''}};
  const root = {
    getAttribute: key => key === 'data-pad-stopwatch' ? String(total) : running ? '1' : '0',
    querySelector: selector => nodes[selector]
  };
  for (const action of ['start', 'pause', 'reset']) {
    nodes[`[data-pad-time-action="${action}"]`] = {
      disabled: true,
      getAttribute: () => action,
      closest: selector => selector === '[data-pad-stopwatch]' ? root : nodes[`[data-pad-time-action="${action}"]`]
    };
  }
  root.click = action => listeners.click({target: nodes[`[data-pad-time-action="${action}"]`]});
  root.text = () => nodes.output.textContent;
  root.status = () => nodes['[data-pad-time-status]'].textContent;
  return root;
}
function clock(zone, format) {
  return {textContent: '', getAttribute: key => key === 'data-pad-clock' ? zone : format};
}
const first = stopwatch(65000), second = stopwatch(0, true);
const amsterdam = clock('Europe/Amsterdam', 'Y-m-d H:i:s');
const tokyo = clock('Asia/Tokyo', 'h:i:s A');
const document = {
  readyState: 'complete',
  querySelectorAll: selector => selector === '[data-pad-stopwatch]' ? [first, second] : [amsterdam, tokyo],
  addEventListener: (event, callback) => { listeners[event] = callback; }
};
class FixedDate extends Date { constructor(...args) { super(...(args.length ? args : [wall])); } }
const context = {window: {}, document, performance: {now: () => monotonic}, Date: FixedDate, Intl, setInterval: callback => {timer = callback;}};
runInNewContext(source, context);
assert.equal(first.text(), '00:01:05.0');
assert.equal(first.status(), 'Paused');
assert.equal(second.status(), 'Running');
assert.equal(amsterdam.textContent, '2026-10-25 02:30:00');
assert.equal(tokyo.textContent, '09:30:00 AM');
first.click('start');
monotonic = 1250; timer();
assert.equal(first.text(), '00:01:06.2');
assert.equal(second.text(), '00:00:01.2');
first.click('pause');
monotonic = 6250; timer();
assert.equal(first.text(), '00:01:06.2');
assert.equal(second.text(), '00:00:06.2');
first.click('start');
monotonic = 7250; timer();
assert.equal(first.text(), '00:01:07.2');
first.click('reset');
assert.equal(first.text(), '00:00:00.0');
assert.equal(first.status(), 'Paused');
wall = new Date('2026-10-25T01:30:00Z'); timer();
assert.equal(amsterdam.textContent, '2026-10-25 02:30:00');
assert.equal(amsterdam.dateTime, '2026-10-25T01:30:00.000Z');
assert.equal(tokyo.textContent, '10:30:00 AM');
const previousTimer = timer;
runInNewContext(source, context);
assert.equal(timer, previousTimer, 'repeated script does not install a second controller');
console.log('Time browser controller: start, pause, resume, reset, independence, DST, clock rollover and script deduplication pass');
