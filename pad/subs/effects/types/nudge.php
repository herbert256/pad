<?php

  // {nudge}<a class="button" href="?signup">Try it free</a>{/nudge} or {nudge 'New'/} - a
  // short movement that draws the eye to something, then lets it be: style= bounce (the
  // default, a hop and a smaller one), shake (side to side, as a field that was filled in
  // wrong), pulse (it swells and settles), wiggle (it rocks about its middle) or heartbeat (two
  // beats). One nudge takes duration= seconds (a second or so, as the style has it), repeat=
  // nudges (2, or forever) one after the other, after delay= seconds. The content stays as it
  // is - a link or a button inside keeps its click and its focus - and nothing around it moves:
  // the nudge is a transform. Under reduced motion it stands still.
  //
  //   {nudge style='shake', delay=1}<p class="error">Fill in your e-mail address</p>{/nudge}

  $padFxSource = padEffectSource ();
  $padFxStyle  = padEffectChoice ( 'style', 'bounce', [ 'bounce', 'shake', 'pulse', 'wiggle', 'heartbeat' ] );
  $padFxTime   = padEffectNumber ( 'duration', [ 'bounce' => 1, 'shake' => 0.8, 'pulse' => 1, 'wiggle' => 0.9, 'heartbeat' => 1.3 ] [$padFxStyle], 0.2, 10 );
  $padFxDelay  = padEffectNumber ( 'delay', 0, 0, 60 );
  $padFxRepeat = padEffectRepeat ( 2 );

  if ( padEffectPlain ( $padFxSource ) === '' and ! preg_match ( '/<(img|svg|picture|video|input|button|select|textarea)\b/i', $padFxSource ) )
    return padEffectNeedsText ( "{nudge}<button>Try it</button>{/nudge} or {nudge 'New'/}" );

  $padFxElement = padEffectBlock ( $padFxSource ) ? 'div' : 'span';

  return padEffectDone ( 'nudge',
    padProtect ( padEffectOpen ( 'nudge', $padFxElement, [
        'duration' => padEffectSeconds ( $padFxTime ),
        'delay'    => padEffectSeconds ( $padFxDelay ),
        'repeat'   => $padFxRepeat
      ], '', "pe-$padFxStyle" ) )
    . $padFxSource
    . padProtect ( "</$padFxElement>" ) );

?>
