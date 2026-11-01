<?php

  // {spinner/} or {spinner style='dots', label='Saving'/} or {spinner}Loading the orders{/spinner}
  // - a loading indicator, the one effect for every loader: style= ring (the default), dots,
  // bars, pulse or orbit. It is a status (role="status"): a screen reader says its label -
  // label=, 'Loading' when not given - and the pair's content is a label shown beside it.
  // size= in pixels (24, 8 to 200); its colour is --pad-effect-accent, color= another. It
  // turns for as long as it is shown - it stands for something going on - and under reduced
  // motion it stands still, its label then shown beside it. (style= is no option of PAD's -
  // nothing in pad/options/ or pad/handling/ has the name - so the spinner may have it.)

  $padFxContent = padEffectContent ();
  $padFxStyle   = padEffectChoice ( 'style', 'ring', [ 'ring', 'dots', 'bars', 'pulse', 'orbit' ] );
  $padFxLabel   = padEffectText ( 'label', 'Loading' );
  $padFxSize    = padEffectInt ( 'size', 24, 8, 200 );
  $padFxColor   = padEffectColor ( 'color', '' );

  $padFxShown = ( $padFxContent !== NULL and trim ( $padFxContent ) !== '' );
  $padFxParts = [ 'ring' => 0, 'dots' => 3, 'bars' => 4, 'pulse' => 0, 'orbit' => 1 ] [$padFxStyle];
  $padFxDraw  = str_repeat ( '<i></i>', $padFxParts );

  if ( $padFxLabel === '' )
    $padFxLabel = 'Loading';

  return padEffectDone ( 'spinner',
    padProtect ( padEffectOpen ( 'spinner', 'span', [
        'size'  => "{$padFxSize}px",
        'color' => $padFxColor
      ], ' role="status"', "pe-$padFxStyle" )
      . "<span class=\"pe-mark\" aria-hidden=\"true\">$padFxDraw</span>" )
    . ( $padFxShown
        ? padProtect ( '<span class="pe-label">' ) . trim ( $padFxContent ) . padProtect ( '</span>' )
        : padProtect ( '<span class="pe-label pad-effect-sr">' ) . padEffectEscape ( $padFxLabel ) . padProtect ( '</span>' ) )
    . padProtect ( '</span>' ) );

?>
