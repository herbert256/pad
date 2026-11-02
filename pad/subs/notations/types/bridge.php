<?php

  // bridge - a deal of contract bridge written as the Deal tag of PBN, the Portable Bridge
  // Notation: the seat of the first hand and a colon, then the four hands clockwise, each its
  // spades, hearts, diamonds and clubs apart by dots, - for a hand not shown. Drawn as the
  // diagram of a bridge column: North at the top, South at the bottom, West and East either side,
  // every suit from the ace down, the compass in the middle. dealer= rings the dealer's seat,
  // vulnerable= - none, ns, ew, all - colours the vulnerable sides red, points writes the
  // high-card points of every hand, title= is the accessible name. A screen reader hears every
  // hand card by card.
  //
  //   {notation:bridge 'N:.63.AKQ987.A9732 A8654.KQ5.T.QJT6 J973.J98742.3.K4 KQT2.AT.J6542.85'/}
  //   {notation:bridge 'W:KQT2.AT.J6542.85 - A8654.KQ5.T.QJT6 -', dealer='W', vulnerable='ew'/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padBridgeRead = padBridgeRead ( $padNotationText );

  if ( is_string ( $padBridgeRead ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padBridgeRead" );
    return '';
  }

  $padBridgeDeal   = $padBridgeRead [0];
  $padBridgeDealer = strtoupper ( padNotationChoice ( 'dealer', '', [ '', 'n', 'e', 's', 'w' ] ) );
  $padBridgeVul    = PAD_BRIDGE_VULNERABLE [ padNotationChoice ( 'vulnerable', 'none', array_keys ( PAD_BRIDGE_VULNERABLE ) ) ];
  $padBridgePoints = (bool) padTagParm ( 'points', FALSE );

  $padBridgeSaid = [ $padBridgeDealer !== '' ? 'dealer ' . PAD_BRIDGE_SEATS [$padBridgeDealer] : '',
                     [ '' => 'no side vulnerable', 'NS' => 'North-South vulnerable', 'EW' => 'East-West vulnerable', 'NSEW' => 'both sides vulnerable' ] [$padBridgeVul] ];

  $padBridgeDesc = [];

  foreach ( [ 'N', 'E', 'S', 'W' ] as $padBridgeSeat )
    $padBridgeDesc [] = PAD_BRIDGE_SEATS [$padBridgeSeat] . ': '
                      . ( $padBridgeDeal [$padBridgeSeat] === NULL ? 'not shown'
                          : padBridgeWords ( $padBridgeDeal [$padBridgeSeat] ) . ( $padBridgePoints ? '; ' . padBridgePoints ( $padBridgeDeal [$padBridgeSeat] ) . ' points' : '' ) ) . '.';

  $padBridgeTitle = padNotationOption ( 'title' );

  if ( $padBridgeTitle === '' )
    $padBridgeTitle = 'Bridge deal, ' . implode ( ', ', array_filter ( $padBridgeSaid ) );

  return padNotationWrap ( padNotationStyle ( 'bridge', PAD_BRIDGE_COLOURS )
       . padBridgeSvg ( $padBridgeDeal, $padBridgeDealer, $padBridgeVul, $padBridgePoints,
                        padNotationId ( 'bridge', [ $padBridgeDeal, $padBridgeDealer, $padBridgeVul, $padBridgePoints, $padBridgeTitle ] ),
                        $padBridgeTitle, implode ( ' ', $padBridgeDesc ) ) );

?>
