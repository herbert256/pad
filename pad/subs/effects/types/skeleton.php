<?php

  // {skeleton/} or {skeleton lines=4/} or {skeleton shape='card'/} - a placeholder in the shape
  // of what is still on its way, a sheen sweeping over it: grey bones where the lines, the
  // picture or the avatar will stand, while a fragment, a live region or a lazy part loads,
  // and gone when what it stands in for replaces it. shape= text (the default: lines= lines,
  // 3, the last one shorter), block (a picture, height= pixels high, 160), avatar (a circle,
  // size= pixels, 48), card (a block, a title and lines) or media (an avatar beside a title
  // and lines, 2). It is a status (role="status"): a screen reader says its label - label=,
  // 'Loading' when not given - the bones are hidden from it. Its colours are the text colour,
  // faint, so a theme colours it; the sheen sweeps for as long as it is shown - it stands for
  // something going on, as the spinner does - and under reduced motion the bones stand
  // still. Only single: what it stands in for is not there yet.
  //
  //   <div hx-get="?orders" hx-trigger="load">{skeleton shape='media', lines=3/}</div>

  $padFxShape  = padEffectChoice ( 'shape', 'text', [ 'text', 'block', 'avatar', 'card', 'media' ] );
  $padFxLines  = padEffectInt ( 'lines', ( $padFxShape == 'media' ) ? 2 : 3, 1, 20 );
  $padFxHeight = padEffectInt ( 'height', 160, 8, 2000 );
  $padFxSize   = padEffectInt ( 'size', 48, 8, 400 );
  $padFxLabel  = padEffectText ( 'label', 'Loading' );

  if ( padEffectContent () !== NULL )
    padEffectBad ( "is written single, it stands for what is not there yet - {skeleton lines=3/}" );

  if ( $padFxLabel === '' )
    $padFxLabel = 'Loading';

  $padFxLine  = str_repeat ( '<span class="pe-bone pe-line"></span>', $padFxLines );
  $padFxTitle = '<span class="pe-bone pe-title"></span>';

  $padFxBones = [
    'text'   => $padFxLine,
    'block'  => '<span class="pe-bone pe-picture"></span>',
    'avatar' => '<span class="pe-bone pe-circle"></span>',
    'card'   => '<span class="pe-bone pe-picture"></span>' . $padFxTitle . $padFxLine,
    'media'  => '<span class="pe-bone pe-circle"></span><span class="pe-lines">' . $padFxTitle . $padFxLine . '</span>'
  ] [$padFxShape];

  return padEffectDone ( 'skeleton',
    padProtect ( padEffectOpen ( 'skeleton', 'div', [
        'height' => in_array ( $padFxShape, [ 'block', 'card' ] ) ? "{$padFxHeight}px" : NULL,
        'size'   => in_array ( $padFxShape, [ 'avatar', 'media' ] ) ? "{$padFxSize}px" : NULL
      ], ' role="status"', "pe-$padFxShape" )
      . "<span class=\"pe-bones\" aria-hidden=\"true\">$padFxBones</span><span class=\"pad-effect-sr\">" )
    . padEffectEscape ( $padFxLabel )
    . padProtect ( '</span></div>' ) );

?>
