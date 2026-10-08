<?php

  // The time left until a moment, in words - the {countdown} tag.
  //
  //   {countdown '2026-12-31 00:00'}              84 days, 3 hours
  //   {countdown $launch, units=3, past='We are live!', live}
  //
  // padCountdownText    the seconds left as the largest units that are there - days,
  //                     hours, minutes, seconds - $units of them from the first that is not
  //                     zero, a zero one among them left out: 84 days, 3 hours; 5 minutes,
  //                     12 seconds. Nothing left is the $past text.
  // padCountdown        the text in a <time> whose datetime is the moment and whose title
  //                     is the moment in full; live adds the moment in milliseconds for the
  //                     script, and role="timer"
  // padCountdownScript  the script of a live countdown - once per page - that counts the
  //                     same way in the browser, once a second, from the browser's clock
  //
  // Now is padNow's - frozen when a test froze it - or the moment now= gives, so the words
  // the server writes can be tested; the browser's ticking starts from its own clock.

  const padCountdownUnits = [ 'day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1 ];

  function padCountdownText ( $seconds, $units, $past ) {

    if ( $seconds <= 0 )
      return $past;

    $parts = [];

    foreach ( padCountdownUnits as $unit => $size ) {
      $parts [$unit] = intdiv ( $seconds, $size );
      $seconds      -= $parts [$unit] * $size;
    }

    while ( count ( $parts ) > 1 and reset ( $parts ) == 0 )
      array_shift ( $parts );

    $words = [];

    foreach ( array_slice ( $parts, 0, $units, TRUE ) as $unit => $count )
      if ( $count )
        $words [] = $count . ' ' . $unit . ( $count == 1 ? '' : 's' );

    return implode ( ', ', $words );

  }

  function padCountdown ( $moment, $now, $units, $past, $live, $format ) {

    $seconds = $moment->getTimestamp () - $now->getTimestamp ();
    $text    = padCountdownText ( $seconds, $units, $past );
    $title   = $moment->setTimezone ( padDateZone () )->format ( $format );

    $html = '<time class="pad-countdown" datetime="' . padChartAttr ( $moment->format ( DATE_ATOM ) ) . '"'
          . ' title="' . padChartAttr ( $title ) . '"';

    if ( $live )
      $html .= ' role="timer" data-pad-countdown="' . ( $moment->getTimestamp () * 1000 ) . '"'
             . ' data-pad-units="' . $units . '" data-pad-past="' . padChartAttr ( $past ) . '"';

    $html .= '>' . padChartAttr ( $text ) . '</time>';

    return $live ? $html . padCountdownScript () : $html;

  }

  // Written behind the first live countdown of the request. Under a Content-Security-Policy
  // that asks for a nonce ('nonce' in $padCsp) the script carries this request's, as
  // {asset} writes it (lib/asset.php); without one an inline script runs as it is.

  function padCountdownScript () {

    global $padCsp;

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . padChartAttr ( padNonce () ) . '"' : '';

    return "<script$nonce>" . <<<'SCRIPT'
(function () {
  if (window.padCountdownReady) return;
  window.padCountdownReady = true;
  var units = [['day', 86400], ['hour', 3600], ['minute', 60], ['second', 1]];
  function words(seconds, count, past) {
    if (seconds <= 0) return past;
    var parts = units.map(function (unit) { var n = Math.floor(seconds / unit[1]); seconds -= n * unit[1]; return [n, unit[0]]; });
    while (parts.length > 1 && parts[0][0] === 0) parts.shift();
    return parts.slice(0, count).filter(function (part) { return part[0]; })
      .map(function (part) { return part[0] + ' ' + part[1] + (part[0] === 1 ? '' : 's'); }).join(', ');
  }
  function tick() {
    document.querySelectorAll('time[data-pad-countdown]').forEach(function (time) {
      var left = Math.floor((+time.getAttribute('data-pad-countdown') - Date.now()) / 1000);
      var text = words(left, +time.getAttribute('data-pad-units') || 2, time.getAttribute('data-pad-past'));
      if (time.textContent !== text) time.textContent = text;
    });
  }
  tick();
  setInterval(tick, 1000);
})();
SCRIPT
    . '</script>';

  }

?>
