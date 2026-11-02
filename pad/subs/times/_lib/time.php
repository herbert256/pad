<?php

  // World clocks and stopwatches share a single nonce-aware, delegated browser controller.
  function padTimeClock ( $moment, $zone, $format, $live, $label ) {
    $local = $moment->setTimezone ( new DateTimeZone ( $zone ) );
    $html = '<span class="pad-time-clock"><span class="pad-time-label">' . padChartAttr ( $label ) . '</span> '
          . '<time datetime="' . padChartAttr ( $local->format ( DATE_ATOM ) ) . '" title="'
          . padChartAttr ( $local->format ( 'Y-m-d H:i:s T' ) ) . '"';
    if ( $live )
      $html .= ' data-pad-clock="' . padChartAttr ( $zone ) . '" data-pad-format="' . padChartAttr ( $format ) . '" aria-live="off"';
    $html .= '>' . padChartAttr ( $local->format ( $format ) ) . '</time></span>';
    return $html . ( $live ? padTimeScript () : '' );
  }

  function padTimeElapsedText ( $seconds ) {
    $tenths = (int) floor ( $seconds * 10 );
    return sprintf ( '%02d:%02d:%02d.%d', intdiv ( $tenths, 36000 ), intdiv ( $tenths, 600 ) % 60, intdiv ( $tenths, 10 ) % 60, $tenths % 10 );
  }

  function padTimeStopwatch ( $elapsed, $running, $label ) {
    $html = '<span class="pad-time-stopwatch" role="group" aria-label="' . padChartAttr ( $label ) . '"'
          . ' data-pad-stopwatch="' . (int) round ( $elapsed * 1000 ) . '" data-pad-running="' . ( $running ? '1' : '0' ) . '">'
          . '<span class="pad-time-label">' . padChartAttr ( $label ) . '</span> '
          . '<output role="timer" aria-live="off" aria-label="Elapsed time">' . padTimeElapsedText ( $elapsed ) . '</output> '
          . '<button type="button" data-pad-time-action="start" disabled>Start</button> '
          . '<button type="button" data-pad-time-action="pause" disabled>Pause</button> '
          . '<button type="button" data-pad-time-action="reset" disabled>Reset</button> '
          . '<span data-pad-time-status role="status">' . ( $running ? 'Running' : 'Paused' ) . '</span>'
          . '<noscript>Enable JavaScript to use the stopwatch controls.</noscript></span>';
    return $html . padTimeScript ();
  }

  function padTimeScript () {
    global $padCsp;
    if ( ! padWidgetOnce ( 'script:time-displays' ) )
      return '';
    $nonce = ( is_string ( $padCsp ?? NULL ) && str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . padChartAttr ( padNonce () ) . '"' : '';
    return "<script$nonce>" . <<<'SCRIPT'
(function () {
  if (window.padTimeDisplaysReady) return;
  window.padTimeDisplaysReady = true;
  var states = new WeakMap(), clocks = new Map(), clockSecond = -1;
  function pad(value) { return String(value).padStart(2, '0'); }
  function elapsed(ms) {
    var tenths = Math.floor(Math.max(0, ms) / 100);
    return pad(Math.floor(tenths / 36000)) + ':' + pad(Math.floor(tenths / 600) % 60) + ':' + pad(Math.floor(tenths / 10) % 60) + '.' + (tenths % 10);
  }
  function value(state, now) { return state.total + (state.running ? now - state.started : 0); }
  function stateFor(node, now) {
    var state = states.get(node);
    if (!state) {
      state = {total: Number(node.getAttribute('data-pad-stopwatch')) || 0, running: node.getAttribute('data-pad-running') === '1', started: now};
      states.set(node, state);
    }
    return state;
  }
  function paint(node, state, now) {
    node.querySelector('output').textContent = elapsed(value(state, now));
    node.querySelector('[data-pad-time-action="start"]').disabled = state.running;
    node.querySelector('[data-pad-time-action="pause"]').disabled = !state.running;
    node.querySelector('[data-pad-time-action="reset"]').disabled = false;
    var status = node.querySelector('[data-pad-time-status]'), text = state.running ? 'Running' : 'Paused';
    if (status.textContent !== text) status.textContent = text;
  }
  function tick() {
    var now = performance.now(), date = new Date();
    document.querySelectorAll('[data-pad-stopwatch]').forEach(function (node) { paint(node, stateFor(node, now), now); });
    var second = Math.floor(date.getTime() / 1000);
    if (second === clockSecond) return;
    clockSecond = second;
    document.querySelectorAll('time[data-pad-clock]').forEach(function (node) {
      var zone = node.getAttribute('data-pad-clock');
      try {
        if (!clocks.has(zone)) clocks.set(zone, new Intl.DateTimeFormat('en-GB-u-nu-latn', {timeZone: zone, hourCycle: 'h23', year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'}));
        var parts = {};
        clocks.get(zone).formatToParts(date).forEach(function (part) { parts[part.type] = part.value; });
        var hour = Number(parts.hour), tokens = {Y: parts.year, m: parts.month, d: parts.day, H: pad(hour), h: pad(hour % 12 || 12), i: parts.minute, s: parts.second, A: hour < 12 ? 'AM' : 'PM'};
        var text = node.getAttribute('data-pad-format').replace(/[YmdHhisA]/g, function (token) { return tokens[token]; });
        if (node.textContent !== text) node.textContent = text;
        node.dateTime = date.toISOString();
        node.title = parts.year + '-' + parts.month + '-' + parts.day + ' ' + pad(hour) + ':' + parts.minute + ':' + parts.second + ' ' + zone;
      } catch (error) { /* Keep the server's valid clock when Intl lacks its timezone. */ }
    });
  }
  document.addEventListener('click', function (event) {
    var button = event.target.closest && event.target.closest('[data-pad-time-action]');
    if (!button || button.disabled) return;
    var node = button.closest('[data-pad-stopwatch]');
    if (!node) return;
    var now = performance.now(), state = stateFor(node, now), action = button.getAttribute('data-pad-time-action');
    if (action === 'start' && !state.running) { state.started = now; state.running = true; }
    if (action === 'pause' && state.running) { state.total = value(state, now); state.running = false; }
    if (action === 'reset') { state.total = 0; state.running = false; state.started = now; }
    paint(node, state, now);
  });
  tick();
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', tick, {once: true});
  setInterval(tick, 100);
})();
SCRIPT
    . '</script>';
  }

?>
