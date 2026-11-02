<?php

  // {effect:decipher}ACCESS GRANTED{/effect:decipher} or {effect:decipher 'ACCESS GRANTED'/} - a short heading that
  // resolves out of scrambled signs: for duration= seconds (1.2, 0.2 to 10) its letters are
  // signs that change, then they settle into the text one after the other, left to right -
  // when it comes into view, after delay= seconds, and again when the pointer comes on it or
  // the focus into it. glyphs= the signs: symbols (the default), letters, digits or binary;
  // mono, monospaced letters, in which the signs take the room of the text exactly. The text
  // itself never changes: it stands in the page all the time, read once by a screen reader
  // and selected and copied as it is, and the signs are a layer over it, hidden from a screen
  // reader and taking no clicks, gone when they settle - the effect's script
  // (www/assets/effects/decipher.js) draws them. Without script and under reduced motion the
  // text simply stands there. A short text, 80 letters at most - not for what has to be read
  // while it changes: a password, a figure, a menu.
  //
  //   <h2>{effect:decipher glyphs='binary', mono}SIGNAL FOUND{/effect:decipher}</h2>

  $padFxSource   = padEffectSource ();
  $padFxText     = padEffectPlain ( $padFxSource );
  $padFxDuration = padEffectNumber ( 'duration', 1.2, 0.2, 10 );
  $padFxDelay    = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxGlyphs   = padEffectChoice ( 'glyphs', 'symbols', [ 'symbols', 'letters', 'digits', 'binary' ] );
  $padFxMono     = padEffectFlag ( 'mono' );

  if ( $padFxText === '' )
    return padEffectNeedsText ( "{effect:decipher}ACCESS GRANTED{/effect:decipher} or {effect:decipher 'ACCESS GRANTED'/}" );

  $padFxCount = count ( padEffectLetters ( $padFxText ) );

  if ( $padFxCount > 80 ) {
    padEffectBad ( "deciphers a short text, 80 letters at most, not $padFxCount" );
    return $padFxSource;
  }

  $padFxConfig = padWidgetAttr ( json_encode ( [ 'duration' => $padFxDuration, 'delay' => $padFxDelay, 'glyphs' => $padFxGlyphs ] ) );
  $padFxScript = padWidgetScript ( 'effect-decipher', padEffectAsset ( 'decipher.js' ) );

  return padEffectDone ( 'decipher',
    padProtect ( padEffectOpen ( 'decipher', 'span', [], " data-pad-decipher=\"$padFxConfig\"", $padFxMono ? 'pe-mono' : '' ) . '<span class="pe-text">' )
    . $padFxSource
    . padProtect ( '</span></span>' ) ) . $padFxScript;

?>
