<?php

  // {effect:shimmer}New in this release{/effect:shimmer} or {effect:shimmer 'New'/} - a band of light that sweeps
  // across the letters and rests, as on something freshly polished: the text is painted with
  // a gradient - --pad-effect-text with a band of --pad-effect-accent - that moves across it
  // in duration= seconds (2.5), repeat= times (3, or forever), after delay= seconds. The
  // letters stay text, read as they are. Where a browser cannot paint a background into
  // letters, and under reduced motion, it is the text in its own colour.

  $padFxSource   = padEffectSource ();
  $padFxDuration = padEffectNumber ( 'duration', 2.5, 0.5, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat   = padEffectRepeat ( 3 );

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{effect:shimmer}New{/effect:shimmer} or {effect:shimmer 'New'/}" );

  return padEffectDone ( 'shimmer',
    padProtect ( padEffectOpen ( 'shimmer', 'span', [
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat
      ], '', 'pad-effect-clip' ) )
    . $padFxSource
    . padProtect ( '</span>' ) );

?>
