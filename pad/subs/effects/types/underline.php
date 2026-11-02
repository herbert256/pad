<?php

  // {effect:underline}the terms{/effect:underline} or {effect:underline 'this'/} - a line drawn under the words from
  // left to right, once: a stroke of emphasis rather than the filled mark of the highlighter,
  // and a link inside keeps its look and its click. style= line (the default), double, dashed
  // or dotted; color= the ink (--pad-effect-accent when not given); thickness= whole pixels
  // (1 to 12, a tenth of the letters when not given); duration= seconds the stroke takes
  // (0.6), delay= seconds before it starts. scroll draws it as it scrolls into view, where
  // the browser has scroll-driven animations; hover draws it while the pointer is on it or
  // the focus is in it and takes it back when they leave - the underline of a link that
  // grows. Over several lines every line gets its stroke. Under reduced motion the line stands
  // drawn, or comes at once on hover; on paper and in forced colours it is the text's own
  // underline.
  //
  //   Read {effect:underline style='double', color='#e11d48'}the small print{/effect:underline} first.

  $padFxSource   = padEffectSource ();
  $padFxStyle    = padEffectChoice ( 'style', 'line', [ 'line', 'double', 'dashed', 'dotted' ] );
  $padFxColor    = padEffectColor ( 'color', '' );
  $padFxThick    = padEffectThickness ();
  $padFxDuration = padEffectNumber ( 'duration', 0.6, 0.1, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxWhen     = padEffectWhen ();

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{effect:underline}this{/effect:underline} or {effect:underline 'this'/}" );

  return padEffectDone ( 'underline',
    padProtect ( padEffectOpen ( 'underline', 'span', [
        'ink'      => $padFxColor,
        'thick'    => $padFxThick,
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay )
      ], '', trim ( "pe-$padFxStyle $padFxWhen" ) ) )
    . $padFxSource
    . padProtect ( '</span>' ) );

?>
