<?php

  // {effect:wave}Hello there{/effect:wave} or {effect:wave 'Hello there'/} - the letters rise and fall one after
  // the other, a wave running through the word. The moving letters are a copy hidden from a
  // screen reader; the text stands once beside it for assistive technology, as written. One
  // wave runs through in duration= seconds (1.6), repeat= times (3, or forever), after delay=
  // seconds; height= is how far a letter rises, in em (0.25, 0.05 to 1). The letters are the
  // text's characters - an accented letter or an emoji with its modifiers counts as one; the
  // markup of a pair's content is not drawn in the wave. Under reduced motion the text stands
  // still.

  $padFxSource   = padEffectSource ();
  $padFxText     = padEffectPlain ( $padFxSource );
  $padFxDuration = padEffectNumber ( 'duration', 1.6, 0.3, 30 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxHeight   = padEffectNumber ( 'height', 0.25, 0.05, 1 );
  $padFxRepeat   = padEffectRepeat ( 3 );

  if ( $padFxText === '' )
    return padEffectNeedsText ( "{effect:wave}Hello there{/effect:wave} or {effect:wave 'Hello there'/}" );

  // A letter's place in the wave is its place among the spans of the copy, a gap counting too:
  // the rule of the n-th child gives it --pad-effect-i, written once per page for each place
  // a wave on it reaches (padEffectDone) - in a style attribute the CSP nonce dropped it.

  $padFxLetters = '';

  foreach ( padEffectLetters ( $padFxText ) as $padFxAt => $padFxLetter ) {

    $padFxLetters .= ( $padFxLetter === ' ' )
      ? padProtect ( '<span class="pe-gap"> </span>' )
      : padProtect ( '<span>' ) . padEffectEscape ( $padFxLetter ) . padProtect ( '</span>' );

    if ( $padFxLetter !== ' ' and padWidgetOnce ( "style:effect-wave-$padFxAt" ) )
      $GLOBALS ['padEffectRules'] [] = '.pad-effect-wave .pe-wave>:nth-child(' . ( $padFxAt + 1 ) . "){--pad-effect-i:$padFxAt}";

  }

  return padEffectDone ( 'wave',
    padProtect ( padEffectOpen ( 'wave', 'span', [
        'duration' => padEffectSeconds ( $padFxDuration ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat,
        'height'   => rtrim ( rtrim ( number_format ( $padFxHeight, 3, '.', '' ), '0' ), '.' ) . 'em'
      ] ) . '<span class="pad-effect-sr">' )
    . $padFxSource
    . padProtect ( '</span><span class="pe-wave" aria-hidden="true">' )
    . $padFxLetters
    . padProtect ( '</span></span>' ) );

?>
