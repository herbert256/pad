<?php

  // {confetti/} or {confetti}<button type="button">Celebrate</button>{/confetti} - a burst of
  // confetti from where it stands: the single tag bursts once, when it comes into view; the
  // pair every time something in it is clicked, its button - the content is shown and works as
  // it is. The one effect with a script, written once per page with the CSP nonce
  // (assets/confetti.js); the pieces fly on a canvas over the window for three seconds, hidden
  // from a screen reader and taking no clicks. pieces= how many (120, 10 to 500), spread= the
  // angle they fly out in, in degrees (70, 10 to 360), colors= two to eight CSS colours (the
  // accent colours and an amber and a green when not given), seed= which pieces: the same seed
  // the same pieces, on every burst and every load (7 when not given). Under reduced motion,
  // and without script, there is no burst.
  //
  //   {confetti pieces=200, colors='#f59e0b, #ef4444, #3b82f6'}<button>Order placed</button>{/confetti}

  $padFxContent = padEffectContent ();
  $padFxPieces  = padEffectInt ( 'pieces', 120, 10, 500 );
  $padFxSpread  = padEffectInt ( 'spread', 70, 10, 360 );
  $padFxColors  = padEffectColors ( 'colors', [], 2, 8 );
  $padFxSeed    = padEffectInt ( 'seed', 7, 0, 2147483647 );

  $padFxConfig = [ 'trigger' => ( $padFxContent === NULL ) ? 'view' : 'click', 'pieces' => $padFxPieces, 'spread' => $padFxSpread, 'seed' => $padFxSeed ];

  if ( $padFxColors )
    $padFxConfig ['colors'] = $padFxColors;

  $padFxJson    = padWidgetAttr ( json_encode ( $padFxConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
  $padFxElement = ( $padFxContent !== NULL and padEffectBlock ( $padFxContent ) ) ? 'div' : 'span';
  $padFxScript  = padWidgetScript ( 'effect-confetti', padEffectAsset ( 'confetti.js' ) );

  if ( $padFxContent === NULL )
    return padEffectDone ( 'confetti', padProtect ( padEffectOpen ( 'confetti', 'span', [], " data-pad-confetti=\"$padFxJson\" aria-hidden=\"true\"" ) . '</span>' ) ) . $padFxScript;

  return padEffectDone ( 'confetti',
    padProtect ( padEffectOpen ( 'confetti', $padFxElement, [], " data-pad-confetti=\"$padFxJson\"" ) )
    . trim ( $padFxContent )
    . padProtect ( "</$padFxElement>" ) ) . $padFxScript;

?>
