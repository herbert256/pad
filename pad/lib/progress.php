<?php

  // Progress bars - the {progress} tag. A value of a maximum as a bar with its label and its
  // percentage, or as a row of steps, rendered on the server: no script, it prints, and a
  // screen reader hears what it is and how far it has come.
  //
  //   {progress 72, max=100, label='Upload'}
  //   {progress 3, max=5, steps, label='Checkout'}
  //
  // padProgress       the bar: the native <progress> element, styled, under a line with the
  //                   label and the percentage
  // padProgressSteps  the steps: a dot per step joined by a line, the steps done filled and
  //                   the current one ringed, as role="progressbar" with its aria values
  // padProgressNumber a number as it is shown: no trailing zeros
  //
  // The colours are custom properties on .pad-progress, light-dark() and written once per
  // page: --pad-progress-track, -text and a slot per colour= - accent (default), success,
  // warning, danger and neutral. size= is small, medium (default) or large.

  const PAD_PROGRESS_COLORS = [ 'accent', 'success', 'warning', 'danger', 'neutral' ];
  const PAD_PROGRESS_SIZES  = [ 'small', 'medium', 'large' ];

  function padProgress ( $value, $max = 100, $label = '', $color = 'accent', $size = 'medium' ) {

    $value   = max ( 0, min ( (float) $max, (float) $value ) );
    $percent = (int) round ( $value / $max * 100 );
    $name    = ( trim ( (string) $label ) !== '' ) ? trim ( (string) $label ) : 'Progress';
    $head    = ( trim ( (string) $label ) !== '' ) ? '<span class="pg-label">' . padProgressAttr ( $label ) . '</span>' : '';

    return padProgressStyle ()
         . "<div class=\"pad-progress pg-$color pg-$size\">"
         . "<span class=\"pg-head\" aria-hidden=\"true\">$head<span class=\"pg-value\">$percent%</span></span>"
         . '<progress aria-label="' . padProgressAttr ( $name ) . '" value="' . padProgressNumber ( $value ) . '" max="' . padProgressNumber ( $max ) . "\">$percent%</progress>"
         . '</div>';

  }

  function padProgressSteps ( $value, $max = 5, $label = '', $color = 'accent', $size = 'medium' ) {

    $max   = (int) $max;
    $value = (int) max ( 0, min ( $max, round ( (float) $value ) ) );
    $name  = ( trim ( (string) $label ) !== '' ) ? trim ( (string) $label ) : 'Progress';
    $head  = ( trim ( (string) $label ) !== '' ) ? '<span class="pg-label">' . padProgressAttr ( $label ) . '</span>' : '';
    $dots  = '';

    for ( $step = 1; $step <= $max; $step++ ) {
      if ( $step > 1 )
        $dots .= '<span class="pg-link' . ( $step <= $value ? ' pg-done' : '' ) . '"></span>';
      $dots .= '<span class="pg-dot' . ( $step < $value ? ' pg-done' : ( $step == $value ? ' pg-done pg-now' : '' ) ) . '"></span>';
    }

    return padProgressStyle ()
         . "<div class=\"pad-progress pg-steps pg-$color pg-$size\" role=\"progressbar\" aria-label=\"" . padProgressAttr ( $name ) . '"'
         . " aria-valuemin=\"0\" aria-valuemax=\"$max\" aria-valuenow=\"$value\" aria-valuetext=\"Step $value of $max\">"
         . "<span class=\"pg-head\" aria-hidden=\"true\">$head<span class=\"pg-value\">$value / $max</span></span>"
         . "<span class=\"pg-dots\" aria-hidden=\"true\">$dots</span>"
         . '</div>';

  }

  function padProgressNumber ( $number ) {

    $text = number_format ( (float) $number, 4, '.', '' );

    return rtrim ( rtrim ( $text, '0' ), '.' );

  }

  // The colours and the rules, once per page. The native element is restyled for both
  // engines: ::-webkit-progress-* for Chromium and Safari, ::-moz-progress-bar for Firefox.

  function padProgressStyle () {

    static $written = FALSE;

    if ( $written )
      return '';

    $written = TRUE;
    $roles   = [ 'track'   => [ '#e4e3df', '#3a3a37' ],
                 'text'    => [ '#52514e', '#c3c2b7' ],
                 'accent'  => [ '#2a78d6', '#3987e5' ],
                 'success' => [ '#158a60', '#1baf7a' ],
                 'warning' => [ '#c98500', '#eda100' ],
                 'danger'  => [ '#d03b3a', '#e66767' ],
                 'neutral' => [ '#7a7974', '#898781' ] ];
    $light   = $both = $slots = '';

    foreach ( $roles as $role => list ( $day, $night ) ) {
      $light .= "--pad-progress-$role:$day;";
      $both  .= "--pad-progress-$role:light-dark($day,$night);";
    }

    foreach ( PAD_PROGRESS_COLORS as $color )
      $slots .= ".pad-progress.pg-$color{--pad-progress-bar:var(--pad-progress-$color)}";

    return '<style' . padProgressNonce () . '>'
         . ":where(.pad-progress){{$light}}"
         . "@supports (color:light-dark(#000,#fff)){:where(.pad-progress){{$both}}}"
         . $slots
         . '.pad-progress{--pg-h:8px;display:grid;gap:6px;min-width:8em;color:var(--pad-progress-text);font:500 13px/1.3 system-ui,-apple-system,"Segoe UI",sans-serif}'
         . '.pad-progress.pg-small{--pg-h:4px;font-size:12px}.pad-progress.pg-large{--pg-h:14px;font-size:14px}'
         . '.pad-progress .pg-head{display:flex;justify-content:space-between;gap:1em}'
         . '.pad-progress .pg-value{margin-left:auto;font-variant-numeric:tabular-nums}'
         . '.pad-progress progress{-webkit-appearance:none;appearance:none;display:block;width:100%;height:var(--pg-h);margin:0;border:0;border-radius:999px;'
         . 'background:var(--pad-progress-track);color:var(--pad-progress-bar);overflow:hidden}'
         . '.pad-progress progress::-webkit-progress-bar{background:var(--pad-progress-track);border-radius:999px}'
         . '.pad-progress progress::-webkit-progress-value{background:var(--pad-progress-bar);border-radius:999px}'
         . '.pad-progress progress::-moz-progress-bar{background:var(--pad-progress-bar);border-radius:999px}'
         . '.pad-progress .pg-dots{display:flex;align-items:center}'
         . '.pad-progress .pg-dot{flex:none;width:calc(var(--pg-h) + 6px);height:calc(var(--pg-h) + 6px);border-radius:50%;'
         . 'background:var(--pad-progress-track);box-sizing:border-box}'
         . '.pad-progress .pg-link{flex:1;height:max(2px,calc(var(--pg-h) / 3));background:var(--pad-progress-track)}'
         . '.pad-progress .pg-done{background:var(--pad-progress-bar)}'
         . '.pad-progress .pg-now{box-shadow:0 0 0 3px color-mix(in srgb,var(--pad-progress-bar) 30%,transparent)}'
         . '</style>';

  }

  function padProgressNonce () {

    global $padCsp;

    return ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) ) ? ' nonce="' . padNonce () . '"' : '';

  }

  function padProgressAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
