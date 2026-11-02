<?php

  // {effect:encircle}the one word{/effect:encircle} or {effect:encircle 'this'/} - a ring drawn round a word or a
  // short phrase as a pen circles it on paper: the stroke goes round once and runs a little
  // past where it began. style= circle (the default, an oval) or box, a rectangle drawn by
  // hand; color= the ink (--pad-effect-accent2 when not given); thickness= whole pixels (1 to
  // 12, 2 when not given); duration= seconds the ring takes (0.9), delay= seconds before it
  // starts. scroll draws it as it scrolls into view, where the browser has scroll-driven
  // animations; hover draws it while the pointer is on it or the focus is in it. The ring is
  // an svg over the words that stretches to them, hidden from a screen reader and taking no
  // clicks - the words stay as they are, a link inside works. A word or a phrase: the ring
  // holds one box, so a long text wraps inside it rather than across lines. Under reduced
  // motion the ring stands drawn, or comes at once on hover.
  //
  //   The answer is {effect:encircle style='box'}42{/effect:encircle}.

  $padFxSource   = padEffectSource ();
  $padFxStyle    = padEffectChoice ( 'style', 'circle', [ 'circle', 'box' ] );
  $padFxColor    = padEffectColor ( 'color', '' );
  $padFxThick    = padEffectThickness ();
  $padFxDuration = padEffectNumber ( 'duration', 0.9, 0.1, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxWhen     = padEffectWhen ();

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{effect:encircle}this{/effect:encircle} or {effect:encircle 'this'/}" );

  // The ring in a square of 100 the svg stretches over the words, the pen going round with
  // the clock - the way the mask that uncovers it turns - from the lower left for the oval,
  // from the upper left for the box, and on a little past its start.

  $padFxPath = [
    'circle' => 'M18 88C3 76 0 36 20 16C38 1 76 0 93 17C104 32 101 70 82 87C64 101 27 100 10 80C6 75 4 69 5 62',
    'box'    => 'M3 9C30 5 70 5 97 7C98 36 98 64 96 93C66 96 34 96 4 94C2 66 2 38 4 4'
  ] [$padFxStyle];

  return padEffectDone ( 'encircle',
    padProtect ( padEffectOpen ( 'encircle', 'span', [
        'ink'      => $padFxColor,
        'thick'    => $padFxThick,
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay )
      ], '', trim ( "pe-$padFxStyle $padFxWhen" ) ) )
    . $padFxSource
    . padProtect ( '<svg class="pe-ring" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
                 . "<path d=\"$padFxPath\"/></svg></span>" ) );

?>
