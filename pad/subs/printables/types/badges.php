<?php

  // {printable:badges data='guests', title='PAD Conference 2026'}...{/printable:badges} - name
  // badges, cut from a sheet or slid into a holder: the content between the tags is one
  // badge - a name in an <h2>, a line under it - centred below a band of colour that holds
  // title=, the name of the event. color= the band (#2a78d6); sheet= a4-2x5 (90 by 55 mm, the
  // default), a4-2x4 (90 by 65) or letter-2x3 (4 by 3 inches), and the grid options of the
  // labels - across=, down=, width=, height=, margin=, pitch=, paper=, skip=; padding=, size=
  // (10 pt) and align= (center) the badge's text. The edges are printed, to cut along.
  //
  //   {printable:badges data='guests', title='PAD Conference 2026', color='#c0392b'}
  //     <h2>{$name}</h2>{$company}
  //   {/printable:badges}

  $padBadgeSheets = [
    'a4-2x5'     => [ 'a4',     2, 5, 90,    55,   95,    55   ],
    'a4-2x4'     => [ 'a4',     2, 4, 90,    65,   95,    68   ],
    'letter-2x3' => [ 'letter', 2, 3, 101.6, 76.2, 101.6, 76.2 ],
  ];

  $padBadgeGrid  = padPrintableGrid ( $padBadgeSheets, 'a4-2x5' );
  $padBadgeBox   = padPrintableBox ( [ 'pad' => 4, 'size' => 10, 'align' => 'center', 'valign' => 'middle' ] );
  $padBadgeColor = padPrintableColor ( 'color', '#2a78d6' );
  $padBadgeTitle = padWidgetAttr ( padPrintableText ( 'title', '' ) );

  return padPrintablePlan ( [

    'name' => [ 'badge', 'badges' ],
    'grid' => $padBadgeGrid,

    'item' => function ( $cell, $parts ) use ( $padBadgeBox, $padBadgeColor, $padBadgeTitle ) {

      list ( $x, $y, $w, $h ) = $cell;

      if ( $parts === NULL )
        return [ padPrintableEdge ( $cell ) ];

      $band = round ( min ( 16, $h * 0.26 ), 3 );

      return [
        [ 'rect', $x, $y, $w, $band, [ 'fill' => $padBadgeColor ] ],
        $padBadgeTitle !== '' ? [ 'box', $x, $y, $w, $band, "<b>$padBadgeTitle</b>",
                                  [ 'pad' => 2, 'size' => 9, 'align' => 'center', 'valign' => 'middle', 'color' => '#ffffff' ] ] : NULL,
        [ 'box', $x, $y + $band, $w, $h - $band, $parts [''], $padBadgeBox ],
        padPrintableEdge ( $cell, TRUE ) ];

    },

  ] );

?>
