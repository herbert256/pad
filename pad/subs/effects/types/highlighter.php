<?php

  // {highlighter}the one thing to remember{/highlighter} or {highlighter 'this'/} - a stroke
  // of a marker pen drawn behind the words, left to right, once: a <mark>, so a screen reader
  // that tells marked text says so, the words read as they are. color= the ink (a CSS colour,
  // --pad-effect-mark when not given: a pale yellow under dark text, a translucent amber under
  // light text in a dark scheme), duration= seconds the stroke takes (0.8), delay= seconds
  // before it starts; scroll draws it as it scrolls into view, where the browser has
  // scroll-driven animations. Over several lines every line gets its stroke. Under reduced
  // motion the words stand marked. (Named highlighter: {highlight} is the tag that colours
  // code, and a pipe.)
  //
  //   Read {highlighter scroll}the terms{/highlighter} before you sign.

  $padFxSource   = padEffectSource ();
  $padFxColor    = padEffectColor ( 'color', '' );
  $padFxDuration = padEffectNumber ( 'duration', 0.8, 0.1, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxScroll   = padEffectFlag ( 'scroll' );

  if ( padEffectPlain ( $padFxSource ) === '' )
    return padEffectNeedsText ( "{highlighter}this{/highlighter} or {highlighter 'this'/}" );

  return padEffectDone ( 'highlighter',
    padProtect ( padEffectOpen ( 'highlighter', 'mark', [
        'mark'     => $padFxColor,
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay )
      ], '', $padFxScroll ? 'pe-scroll' : '' ) )
    . $padFxSource
    . padProtect ( '</mark>' ) );

?>
