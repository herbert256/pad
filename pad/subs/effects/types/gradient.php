<?php

  // {gradient}Build pages, not plumbing{/gradient} or {gradient 'PAD'/} - text painted with a
  // gradient: colors= two to six CSS colours between commas, the accent colours
  // --pad-effect-accent and --pad-effect-accent2 when not given; angle= the direction in
  // degrees (90, left to right). animate lets the colours flow along the letters, one pass in
  // duration= seconds (4), repeat= times (3, or forever), after delay= seconds - still under
  // reduced motion. The letters stay text; where a browser cannot paint into letters they
  // are the text's own colour.
  //
  //   {gradient colors='#f97316, #db2777, #7c3aed', animate}Launch week{/gradient}

  $padFxSource   = padEffectSource ();
  $padFxColors   = padEffectColors ( 'colors', [], 2, 6 );
  $padFxAngle    = padEffectInt ( 'angle', 90, 0, 360 );
  $padFxAnimate  = padEffectFlag ( 'animate' );
  $padFxDuration = padEffectNumber ( 'duration', 4, 0.5, 60 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat   = padEffectRepeat ( 3 );

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{gradient}Launch week{/gradient} or {gradient 'PAD'/}" );

  // Flowing, the gradient is twice as wide as the text and starts again with its first
  // colour, so a pass ends where the next one starts.

  if ( $padFxColors and $padFxAnimate )
    $padFxColors [] = $padFxColors [0];

  return padEffectDone ( 'gradient',
    padProtect ( padEffectOpen ( 'gradient', 'span', [
        'angle'    => "{$padFxAngle}deg",
        'colors'   => $padFxColors ? implode ( ',', $padFxColors ) : NULL,
        'duration' => $padFxAnimate ? padEffectSeconds ( $padFxDuration ) : NULL,
        'delay'    => $padFxAnimate ? padEffectSeconds ( $padFxDelay ) : NULL,
        'repeat'   => $padFxAnimate ? $padFxRepeat : NULL
      ], '', 'pad-effect-clip' . ( $padFxAnimate ? ' pe-flow' : '' ) ) )
    . $padFxSource
    . padProtect ( '</span>' ) );

?>
