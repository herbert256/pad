<?php

  // Bridge deals - the bridge notation, {notation:bridge 'N:.63.AKQ987.A9732 A8654.KQ5.T.QJT6 ...'/}. A
  // deal of contract bridge written as the Deal tag of PBN, the Portable Bridge Notation (version
  // 2.1, section 3.4.11), drawn as the diagram a bridge column prints: North at the top, South
  // at the bottom, West and East either side, each hand by suit - spades, hearts, diamonds,
  // clubs - its cards from the ace down, the compass in the middle with the dealer ringed and
  // the vulnerable sides red.
  //
  // padBridgeRead   the hands of a deal: by seat, each suit's ranks from the ace down, NULL for
  //                 a hand not given (-) - or what is wrong with it
  // padBridgePoints the high-card points of a hand: an ace 4, a king 3, a queen 2, a jack 1
  // padBridgeSvg    the diagram
  //
  // PBN writes a deal as the seat of its first hand, a colon and the four hands clockwise from
  // it, apart by spaces; a hand is its four suits in the order spades, hearts, diamonds, clubs,
  // apart by dots, a suit its ranks A K Q J T 9 ... 2, a void left empty, an unknown hand "-".
  // The spec's example is N:.63.AKQ987.A9732 A8654.KQ5.T.QJT6 J973.J98742.3.K4
  // KQT2.AT.J6542.85, and W:KQT2.AT.J6542.85 - A8654.KQ5.T.QJT6 - with two hands unknown. Its
  // import format takes the ranks in any order; 10 is read as T as well. Without the seat and
  // its colon the first hand is North's.

  const PAD_BRIDGE_COLOURS = [ 'text' => [ '#111827', '#f3f4f6' ], 'red' => [ '#c8102e', '#ff6b7d' ],
                               'muted' => [ '#6b7280', '#9ca3af' ], 'table' => [ '#e7efe9', '#1f3a2c' ],
                               'line' => [ '#9ca3af', '#4b5563' ] ];

  const PAD_BRIDGE_SEATS = [ 'N' => 'North', 'E' => 'East', 'S' => 'South', 'W' => 'West' ];
  const PAD_BRIDGE_SUITS = [ [ '♠', 'spades', FALSE ], [ '♥', 'hearts', TRUE ], [ '♦', 'diamonds', TRUE ], [ '♣', 'clubs', FALSE ] ];
  const PAD_BRIDGE_RANKS = 'AKQJT98765432';
  const PAD_BRIDGE_WORDS = [ 'A' => 'ace', 'K' => 'king', 'Q' => 'queen', 'J' => 'jack', 'T' => '10' ];

  // The vulnerability of the Vulnerable tag of PBN (3.4.10): None, Love or - no side, NS, EW,
  // All or Both.

  const PAD_BRIDGE_VULNERABLE = [ 'none' => '', 'love' => '', '-' => '', 'ns' => 'NS', 'ew' => 'EW', 'all' => 'NSEW', 'both' => 'NSEW' ];

  // [ hands by seat N E S W - each four lists of ranks, or NULL - ] or a string.

  function padBridgeRead ( $text ) {

    $text  = trim ( preg_replace ( '/\s+/u', ' ', (string) $text ) );
    $first = 'N';

    if ( preg_match ( '/^([NESWnesw])\s*:\s*(.*)$/u', $text, $m ) ) {
      $first = strtoupper ( $m [1] );
      $text  = $m [2];
    } elseif ( preg_match ( '/^([^:\s]*):/u', $text, $m ) )
      return "a first seat '" . padNotationShow ( $m [1] ) . "' - a deal starts with N, E, S or W and a colon, N:";

    $hands = explode ( ' ', $text );

    if ( count ( $hands ) != 4 )
      return count ( $hands ) . ' ' . ( count ( $hands ) == 1 ? 'hand' : 'hands' ) . " - a deal is four hands apart by spaces, clockwise from the first seat, - for one not shown";

    $order  = array_keys ( PAD_BRIDGE_SEATS );
    $start  = array_search ( $first, $order );
    $deal   = [];
    $seen   = [];

    foreach ( $hands as $at => $hand ) {

      $seat = $order [ ( $start + $at ) % 4 ];

      if ( $hand === '-' ) {
        $deal [$seat] = NULL;
        continue;
      }

      $suits = explode ( '.', str_replace ( '10', 'T', $hand ) );

      if ( count ( $suits ) != 4 )
        return "a hand '" . padNotationShow ( $hand ) . "' for " . PAD_BRIDGE_SEATS [$seat] . " - a hand is its spades, hearts, diamonds and clubs apart by dots, AK.QJ2.T98.765";

      foreach ( $suits as $suit => $ranks ) {
        $ranks = strtoupper ( $ranks );
        $list  = [];
        foreach ( str_split ( $ranks ) as $rank ) {
          if ( $rank === '' )
            continue;
          if ( ! str_contains ( PAD_BRIDGE_RANKS, $rank ) )
            return "a card '" . padNotationShow ( $rank ) . "' in the " . PAD_BRIDGE_SUITS [$suit] [1] . " of " . PAD_BRIDGE_SEATS [$seat] . " - the ranks are A K Q J T 9 8 7 6 5 4 3 2";
          if ( isset ( $seen [$suit] [$rank] ) )
            return "the " . padBridgeCard ( $suit, $rank ) . " twice - in the hands of " . PAD_BRIDGE_SEATS [ $seen [$suit] [$rank] ] . " and " . PAD_BRIDGE_SEATS [$seat];
          $seen [$suit] [$rank] = $seat;
          $list [] = $rank;
        }
        usort ( $list, fn ( $a, $b ) => strpos ( PAD_BRIDGE_RANKS, $a ) <=> strpos ( PAD_BRIDGE_RANKS, $b ) );
        $suits [$suit] = $list;
      }

      $deal [$seat] = $suits;

    }

    // Every hand shown holds as many cards - 13 in a whole deal, fewer in an ending.

    $counts = [];

    foreach ( $deal as $seat => $hand )
      if ( $hand !== NULL )
        $counts [$seat] = array_sum ( array_map ( 'count', $hand ) );

    if ( ! $counts )
      return "no hand - every hand is -";

    if ( max ( $counts ) > 13 )
      return "a hand of " . max ( $counts ) . " cards for " . PAD_BRIDGE_SEATS [ array_search ( max ( $counts ), $counts ) ] . " - a hand holds 13 at most";

    if ( count ( array_unique ( $counts ) ) > 1 )
      return "hands of " . implode ( ' and ', array_unique ( $counts ) ) . " cards - every hand holds as many, 13 in a whole deal";

    return [ $deal ];

  }

  function padBridgeCard ( $suit, $rank ) {

    return ( PAD_BRIDGE_WORDS [$rank] ?? $rank ) . ' of ' . PAD_BRIDGE_SUITS [$suit] [1];

  }

  function padBridgePoints ( $hand ) {

    $points = 0;

    foreach ( $hand as $ranks )
      foreach ( $ranks as $rank )
        $points += [ 'A' => 4, 'K' => 3, 'Q' => 2, 'J' => 1 ] [$rank] ?? 0;

    return $points;

  }

  // A hand in words: spades ace, king, 2; hearts none ...

  function padBridgeWords ( $hand ) {

    $said = [];

    foreach ( $hand as $suit => $ranks )
      $said [] = PAD_BRIDGE_SUITS [$suit] [1] . ' ' . ( $ranks ? implode ( ', ', array_map ( fn ( $rank ) => PAD_BRIDGE_WORDS [$rank] ?? $rank, $ranks ) ) : 'none' );

    return implode ( '; ', $said );

  }

  // The width a hand takes: the widest of its suits, its symbol and its ranks at about 9 pixels
  // a character with a space between - 10 two characters - at least 110.

  function padBridgeWidth ( $hand ) {

    $width = 110;

    foreach ( $hand ?? [] as $ranks )
      $width = max ( $width, 24 + array_sum ( array_map ( fn ( $rank ) => $rank === 'T' ? 22 : 13, $ranks ) ) );

    return $width;

  }

  // The diagram: West, the column of North, the compass and South, then East, each hand its
  // seat named over its four suits; the compass 96 square under North.

  function padBridgeSvg ( $deal, $dealer, $vulnerable, $points, $id, $title, $desc ) {

    $west   = padBridgeWidth ( $deal ['W'] );
    $middle = max ( 116, padBridgeWidth ( $deal ['N'] ), padBridgeWidth ( $deal ['S'] ) );
    $east   = padBridgeWidth ( $deal ['E'] );
    $centre = 6 + $west + 10;
    $right  = $centre + $middle + 10;
    $width  = $right + $east + 6;
    $places = [ 'N' => [ $centre, 6 ], 'W' => [ 6, 106 ], 'E' => [ $right, 106 ], 'S' => [ $centre, 210 ] ];
    $svg    = '';

    foreach ( $places as $seat => list ( $x, $y ) ) {

      $hand = $deal [$seat];

      if ( $hand === NULL )
        continue;

      $svg .= '<text class="pbg-seat" x="' . $x . '" y="' . ( $y + 12 ) . '">' . PAD_BRIDGE_SEATS [$seat]
            . ( $points ? ' · ' . padBridgePoints ( $hand ) : '' ) . '</text>';

      foreach ( $hand as $suit => $ranks )
        $svg .= '<text class="pbg-hand" x="' . $x . '" y="' . ( $y + 32 + $suit * 18 ) . '">'
              . '<tspan class="' . ( PAD_BRIDGE_SUITS [$suit] [2] ? 'pbg-red' : 'pbg-black' ) . '">' . PAD_BRIDGE_SUITS [$suit] [0] . '</tspan> '
              . ( $ranks ? implode ( ' ', array_map ( fn ( $rank ) => $rank === 'T' ? '10' : $rank, $ranks ) ) : '—' ) . '</text>';

    }

    // The compass: the vulnerable sides red, the dealer's letter ringed.

    $left = $centre + 10;
    $svg .= '<rect class="pbg-table" x="' . $left . '" y="116" width="96" height="96" rx="6"/>';

    $edges = [ 'N' => [ $left, 116, 96, 10 ], 'S' => [ $left, 202, 96, 10 ], 'W' => [ $left, 116, 10, 96 ], 'E' => [ $left + 86, 116, 10, 96 ] ];

    foreach ( $edges as $seat => list ( $x, $y, $w, $h ) )
      if ( str_contains ( $vulnerable, $seat ) )
        $svg .= '<rect class="pbg-vul" x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '"/>';

    $letters = [ 'N' => [ $left + 48, 138 ], 'S' => [ $left + 48, 198 ], 'W' => [ $left + 22, 168 ], 'E' => [ $left + 74, 168 ] ];

    foreach ( $letters as $seat => list ( $x, $y ) ) {
      if ( $seat === $dealer )
        $svg .= '<circle class="pbg-dealer" cx="' . $x . '" cy="' . ( $y - 5 ) . '" r="10"/>';
      $svg .= '<text class="pbg-compass" x="' . $x . '" y="' . $y . '" text-anchor="middle">' . $seat . '</text>';
    }

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-bridge" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $width . '" height="308" viewBox="0 0 ' . $width . ' 308">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

?>
