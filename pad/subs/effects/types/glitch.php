<?php

  // {glitch}SIGNAL LOST{/glitch} or {glitch 'SIGNAL LOST'/} - a digital glitch: for a moment
  // the text tears into slices that jump aside in two colours, then it stands still again.
  // The slices are two copies of the text laid over it, hidden from a screen reader - the
  // text is there once, as written. A burst opens every cycle of duration= seconds (2.5),
  // repeat= cycles (3, or forever), after delay= seconds; the pointer on it, or the focus in
  // it, plays a burst again. The colours are --pad-effect-accent2 and --pad-effect-accent3.
  // Under reduced motion the text stands still, without the copies.

  $padFxSource   = padEffectSource ();
  $padFxText     = padEffectPlain ( $padFxSource );
  $padFxDuration = padEffectNumber ( 'duration', 2.5, 0.5, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat   = padEffectRepeat ( 3 );

  if ( $padFxText === '' )
    return padEffectNeedsText ( "{glitch}SIGNAL LOST{/glitch} or {glitch 'SIGNAL LOST'/}" );

  $padFxCopy = padEffectEscape ( $padFxText );

  return padEffectDone ( 'glitch',
    padProtect ( padEffectOpen ( 'glitch', 'span', [
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat
      ] ) . '<span class="pe-text">' )
    . $padFxSource
    . padProtect ( '</span>' )
    . padProtect ( '<span class="pe-layer pe-a" aria-hidden="true">' ) . $padFxCopy . padProtect ( '</span>' )
    . padProtect ( '<span class="pe-layer pe-b" aria-hidden="true">' ) . $padFxCopy . padProtect ( '</span></span>' ) );

?>
