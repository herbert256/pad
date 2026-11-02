<?php

  // {effect:reveal}<div class="card">...</div>{/effect:reveal} - a block that comes into view as it is
  // scrolled to: it fades in while it slides up, as far as the scrolling has taken it - a
  // scroll-driven animation (animation-timeline: view()), no script. motion= how it comes:
  // up (the default), down, left, right, fade or zoom; distance= how far it travels, in
  // pixels (32, 0 to 400). Where the browser has no scroll-driven animations, and under
  // reduced motion, the block simply stands there - shown, never hidden. A block already in
  // view when the page opens stands as it is. Only a pair: it wraps what it reveals.
  //
  //   {effect:reveal motion='left'}<p>Every page is a template.</p>{/effect:reveal}

  $padFxContent  = padEffectContent ();
  $padFxMotion   = padEffectChoice ( 'motion', 'up', [ 'up', 'down', 'left', 'right', 'fade', 'zoom' ] );
  $padFxDistance = padEffectInt ( 'distance', 32, 0, 400 );

  if ( $padFxContent === NULL ) {
    padEffectBad ( "wraps what it reveals - {effect:reveal}<p>...</p>{/effect:reveal}" );
    return '';
  }

  return padEffectDone ( 'reveal',
    padProtect ( padEffectOpen ( 'reveal', 'div', [
        'distance' => "{$padFxDistance}px"
      ], '', "pe-$padFxMotion" ) )
    . trim ( $padFxContent )
    . padProtect ( '</div>' ) );

?>
