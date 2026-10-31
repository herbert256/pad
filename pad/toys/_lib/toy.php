<?php

  // The toys subsystem: small live simulations and visual toys played in the browser.
  //
  //   {toy 'life', pattern='glider'/}
  //   {toy:mandelbrot iterations=400/}
  //   {boids birds=150/}
  //   {lsystem}
  //     axiom: X
  //     X: F+[[X]-X]-F[-FX]+X
  //     F: FF
  //     angle: 25
  //   {/lsystem}
  //
  // A toy is a file of toys/types/ - <toy>.php checks the options the toy is given and hands
  // them on - and the files there are the toys there are (padToyTypes, lib/toy.php);
  // toys/assets/ holds its script, <toy>.js, which runs it, and its style, <toy>.css.
  // toys/toy.php is the body the {toy} tag and the toy: type both run; a toy's file runs in
  // padToyTag and answers padToy ( ... ), the frame every toy shares: its name, a status line
  // its script writes to, the stage it draws on, a line for its counters, a bar for its
  // controls, and its configuration as JSON for the script.
  //
  // The script of the toys - assets/toy.js, the runtime: a seeded random number, the canvas
  // with its colours resolved for the page's scheme, the pointer, the controls, the animation
  // loop that pauses while the page is hidden or the toy is scrolled away and starts paused
  // when the visitor asks for reduced motion - and the script of each kind are written once
  // per page with the CSP nonce, as the interactive tags write theirs (lib/widget.php). A
  // toy's script finds every stage of its kind on the page, the ones a fragment or a live
  // region brings later as well. seed= makes what a toy picks at random the same on every
  // load; without it the browser picks.

  // A toy of one kind: its file in toys/types/ sets it up. The file runs in this function,
  // with the source - the content between the toy's tags - at hand to padToySource in
  // $padToyNow, put back afterwards for a toy set up while another one is.

  function padToyTag ( $kind, $source = '' ) {

    global $padToyNow;

    $before    = $padToyNow ?? NULL;
    $padToyNow = [ 'kind' => $kind, 'source' => (string) $source ];

    $html = include PAD . "toys/types/$kind.php";

    $padToyNow = $before;

    return $html;

  }

  // The content between the toy's tags, as written - its shared indent taken off.

  function padToySource () {

    return padChartDedent ( (string) ( $GLOBALS ['padToyNow'] ['source'] ?? '' ) );

  }

  // The lines of the content, trimmed, the empty ones and the # comments left out.

  function padToyLines () {

    $lines = [];

    foreach ( preg_split ( '/\R/', padToySource () ) as $line )
      if ( ( $line = trim ( $line ) ) !== '' and $line [0] != '#' )
        $lines [] = $line;

    return $lines;

  }

  // The tag the toy is written as, for what a message says: {toy 'x'}, {toy:x} or {x}.

  function padToyWhat () {

    global $pad, $padType, $padPrefix, $padToyNow;

    $kind = $padToyNow ['kind'] ?? '';

    if ( ( $padType [$pad] ?? '' ) == 'toy' )
      return ( ( $padPrefix [$pad] ?? '' ) !== '' ? 'toy:' : '' ) . $kind;

    return "toy '$kind'";

  }

  // The first value the toy is given - the parameter of {lsystem 'dragon'} and {toy:lsystem
  // 'dragon'}, the second of {toy 'lsystem', 'dragon'}, whose first names the toy - as text.

  function padToyValue ( $default = '' ) {

    global $pad, $padType, $padOpt;

    $value = $padOpt [$pad] [ ( ( $padType [$pad] ?? '' ) == 'pad' ) ? 2 : 1 ] ?? $default;

    return is_string ( $value ) ? padParmText ( $value ) : $value;

  }

  // What an option was given as, for a message: the value cut short and made safe.

  function padToyShow ( $value ) {

    return padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 );

  }

  // A whole number option, held to its bounds: one that is no whole number, or out of them,
  // is named under the strict check and takes the default.

  function padToyInt ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = trim ( padParmText ( $value ) );

    if ( ( is_int ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?\d+$/D', $value ) ) )
         and (int) $value >= $min and (int) $value <= $max )
      return (int) $value;

    if ( $padCheckSyntax )
      padError ( "the " . padToyWhat () . " has no $option '" . padToyShow ( $value ) . "' - a whole number from $min to $max" );

    return $default;

  }

  // A number option that may have decimals - gravity=9.81 - held to its bounds the same way.

  function padToyNumber ( $option, $default, $min, $max ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );

    if ( is_string ( $value ) )
      $value = trim ( padParmText ( $value ) );

    if ( ( is_int ( $value ) or is_float ( $value ) or ( is_string ( $value ) and preg_match ( '/^-?(\d+\.?\d*|\.\d+)$/D', $value ) ) )
         and is_finite ( (float) $value ) and (float) $value >= $min and (float) $value <= $max )
      return $value + 0;

    if ( $padCheckSyntax )
      padError ( "the " . padToyWhat () . " has no $option '" . padToyShow ( $value ) . "' - a number from $min to $max" );

    return $default;

  }

  // A list of numbers in one option - angles='120, 150' - each held to the bounds, and the
  // list to its length: one that is not is named under the strict check and takes the
  // default.

  function padToyNumbers ( $option, $default, $min, $max, $fewest, $most ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, NULL );

    if ( $value === NULL )
      return $default;

    $list = [];

    foreach ( explode ( ',', is_scalar ( $value ) ? padParmText ( (string) $value ) : '' ) as $one ) {
      $one = trim ( $one );
      if ( ! preg_match ( '/^-?(\d+\.?\d*|\.\d+)$/D', $one ) or (float) $one < $min or (float) $one > $max ) {
        $list = NULL;
        break;
      }
      $list [] = $one + 0;
    }

    if ( $list !== NULL and count ( $list ) >= $fewest and count ( $list ) <= $most )
      return $list;

    if ( $padCheckSyntax )
      padError ( "the " . padToyWhat () . " has no $option '" . padToyShow ( $value ) . "' - "
                 . ( ( $fewest == $most ) ? $fewest : "$fewest to $most" ) . " numbers from $min to $max, between commas" );

    return $default;

  }

  // An option that is one of a few words, named under the strict check when it is another.

  function padToyChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_string ( $value ) ? strtolower ( trim ( padParmText ( $value ) ) ) : $value;

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the " . padToyWhat () . " has no $option '" . padToyShow ( $value ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // A bare option given or not - {life wrap/}.

  function padToyFlag ( $option ) {

    $value = padTagParm ( $option, FALSE );

    return ( $value !== FALSE and $value !== '' and $value !== 0 and $value !== '0' );

  }

  // The toy on the page: the frame, the configuration for the script, the style and the
  // script once per page. title= names the toy, else its own name does; seed= is handed to
  // the script, and paused has it wait for the Play button - as every toy waits when the
  // visitor asks for reduced motion.

  function padToy ( $kind, $name, $config = [] ) {

    $title = (string) padParmText ( padTagParm ( 'title', '' ) );
    $label = ( $title !== '' ) ? $title : $name;

    if ( padTagParm ( 'seed', NULL ) !== NULL )
      $config ['seed'] = padToyInt ( 'seed', 1, 0, 2147483647 );

    if ( padToyFlag ( 'paused' ) )
      $config ['paused'] = TRUE;

    $config ['name'] = $label;

    $id   = padWidgetId ( "toy-$kind", $config );
    $json = padWidgetAttr ( json_encode ( $config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    $show = padWidgetAttr ( $label );

    return padToyStyle ( $kind )
         . padProtect ( "<section class=\"pad-toy pad-toy-$kind\" id=\"$id\" data-pad-toy=\"$kind\" data-config=\"$json\" aria-label=\"$show\">"
                      . "<header class=\"pt-head\"><strong class=\"pt-title\">$show</strong>"
                      . '<span class="pt-status" role="status" aria-live="polite"></span></header>'
                      . '<div class="pt-stage"></div>'
                      . '<div class="pt-info"></div>'
                      . '<div class="pt-bar"></div>'
                      . '<noscript><p class="pt-noscript">This toy runs in the browser and needs JavaScript.</p></noscript>'
                      . '</section>' )
         . padWidgetScript ( 'toy', padFileGet ( PAD . 'toys/assets/toy.js' ) )
         . padWidgetScript ( "toy-$kind", padFileGet ( PAD . "toys/assets/$kind.js" ) );

  }

  // The look of the toys - assets/toy.css, with the colours every toy draws in as custom
  // properties with light and dark defaults - and the look of the kind, assets/<toy>.css,
  // once per page each. canvas is what a toy draws on, following the page's scheme; night is
  // a sky, dark in both, for the toys that need one - fireworks, stars, planets - and glow
  // the light drawn on it.

  function padToyStyle ( $kind ) {

    $roles = [ 'ink'     => [ '#1a1a19', '#f4f3ee' ], 'muted'   => [ '#6b6a65', '#a8a79f' ],
               'line'    => [ '#c9c8c2', '#4a4a46' ], 'surface' => [ '#fcfcfb', '#1a1a19' ],
               'canvas'  => [ '#ffffff', '#10100f' ], 'grid'    => [ '#ecebe6', '#2a2a27' ],
               'accent'  => [ '#2a78d6', '#5598e7' ], 'accent2' => [ '#eb6834', '#e8875f' ],
               'accent3' => [ '#8a4fd3', '#a98ae8' ], 'good'    => [ '#1a8a5a', '#3fbf86' ],
               'bad'     => [ '#d43c3b', '#ef6b6a' ], 'mark'    => [ '#d99a00', '#f2c94c' ],
               'night'   => [ '#0b1020', '#05070d' ], 'glow'    => [ '#fbf7ea', '#fbf7ea' ] ];

    $css = padWidgetStyle ( 'toy', $roles, padFileGet ( PAD . 'toys/assets/toy.css' ) );

    if ( padWidgetOnce ( "style:toy-$kind" ) and padFileExists ( PAD . "toys/assets/$kind.css" ) )
      $css .= padProtect ( '<style' . padStyleNonce () . '>' . padFileGet ( PAD . "toys/assets/$kind.css" ) . '</style>' );

    return $css;

  }

?>
