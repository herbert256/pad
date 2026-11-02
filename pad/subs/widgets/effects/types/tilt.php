<?php

  // {effect:tilt}<div class="card">...</div>{/effect:tilt} - a card that leans in 3D when the pointer is on
  // it or the focus is in it, and settles again when it leaves: CSS alone, so it leans the
  // same way wherever the pointer is - toward= the corner that comes toward the viewer,
  // top-right (the default), top-left, bottom-right or bottom-left; angle= how far, in
  // degrees (8, 1 to 30). glare adds a sheen that slides over it as it leans. The content
  // keeps its links and fields, and nothing moves the page around it. Under reduced motion it
  // does not lean. Only a pair: it wraps what it tilts.
  //
  //   {effect:tilt angle=6, glare}<article class="plan">...</article>{/effect:tilt}

  $padFxContent = padEffectContent ();
  $padFxAngle   = padEffectInt ( 'angle', 8, 1, 30 );
  $padFxToward  = padEffectChoice ( 'toward', 'top-right', [ 'top-right', 'top-left', 'bottom-right', 'bottom-left' ] );
  $padFxGlare   = padEffectFlag ( 'glare' );

  if ( $padFxContent === NULL ) {
    padEffectBad ( "wraps what it tilts - {effect:tilt}<div class=\"card\">...</div>{/effect:tilt}" );
    return '';
  }

  // The corner that comes toward the viewer: its edge across turns forward round the x axis
  // (a top edge by a negative angle), its edge up and down round the y axis (a right edge by
  // a negative angle) - the lean about the horizontal axis three fifths of the other.

  [ $padFxX, $padFxY ] = [ 'top-right' => [ -1, -1 ], 'top-left' => [ -1, 1 ], 'bottom-right' => [ 1, -1 ], 'bottom-left' => [ 1, 1 ] ] [$padFxToward];

  return padEffectDone ( 'tilt',
    padProtect ( padEffectOpen ( 'tilt', 'div', [
        'rx' => round ( $padFxX * $padFxAngle * 0.6, 2 ) . 'deg',
        'ry' => ( $padFxY * $padFxAngle ) . 'deg'
      ], '', $padFxGlare ? 'pe-glare' : '' ) )
    . trim ( $padFxContent )
    . padProtect ( '</div>' ) );

?>
