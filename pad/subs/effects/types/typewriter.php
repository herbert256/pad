<?php

  // {typewriter}Hello, world{/typewriter} or {typewriter 'Hello, world'/} - a line of text typed
  // out letter by letter behind a caret, once, then the caret blinks a few times and stays. The
  // CSS uncovers the text from left to right in as many steps as it has letters - the text
  // itself is whole in the page all the time, so a screen reader reads it at once, never in
  // frames. One line: it does not wrap. speed= letters a second (14, 1 to 100), delay= seconds
  // before it starts (0, up to 60), caret= bar or none, mono - the typewriter's own
  // monospaced letters, in which every step is one letter exactly. Under reduced motion the
  // line stands typed, without a caret.
  //
  //   {typewriter speed=20, mono}npm install pad{/typewriter}

  $padFxSource = padEffectSource ();
  $padFxCount  = count ( padEffectLetters ( padEffectPlain ( $padFxSource ) ) );
  $padFxSpeed  = padEffectNumber ( 'speed', 14, 1, 100 );
  $padFxDelay  = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxCaret  = padEffectChoice ( 'caret', 'bar', [ 'bar', 'none' ] );
  $padFxMono   = padEffectFlag ( 'mono' );

  if ( $padFxCount == 0 )
    return padEffectNeedsText ( "{typewriter}Hello{/typewriter} or {typewriter 'Hello'/}" );

  $padFxTime = $padFxCount / $padFxSpeed;

  return padEffectDone ( 'typewriter',
    padProtect ( padEffectOpen ( 'typewriter', 'span', [
        'chars'    => $padFxCount,
        'duration' => padEffectSeconds ( $padFxTime ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'blinks'   => (int) ceil ( $padFxTime / .8 ) + 4
      ], '', $padFxMono ? 'pe-mono' : '' ) . '<span class="pe-text">' )
    . $padFxSource
    . padProtect ( '</span>' . ( $padFxCaret == 'none' ? '' : '<span class="pe-caret" aria-hidden="true"></span>' ) . '</span>' ) );

?>
