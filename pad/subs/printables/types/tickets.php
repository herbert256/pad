<?php

  // {printable:tickets data='seats'}...<aside>...</aside>{/printable:tickets} - tickets with a
  // stub to tear off: the content between the tags is one ticket, its <aside> the stub - a
  // {barcode:qr} of the ticket's code, a seat - and the rest the main part, with a dashed perforation
  // between them; without an <aside> the stub holds the ticket's number, No. 0001 on, from
  // number= on. sheet= a4-1x4 (190 by 65 mm, the default), a4-1x5 (190 by 52) or letter-1x4
  // (7.75 by 2.5 inches), and the grid options of the labels; padding=, size= (10 pt) and
  // align= the main part's text. The edges are printed, to cut along.
  //
  //   {printable:tickets data='seats'}
  //     <h2>{$show}</h2>{$date} - row {$row}, seat {$seat}
  //     <aside>{barcode:qr $code, size=90/}{$row}-{$seat}</aside>
  //   {/printable:tickets}

  $padTicketSheets = [
    'a4-1x4'     => [ 'a4',     1, 4, 190,    65,   190,    70     ],
    'a4-1x5'     => [ 'a4',     1, 5, 190,    52,   190,    55     ],
    'letter-1x4' => [ 'letter', 1, 4, 196.85, 63.5, 196.85, 66.675 ],
  ];

  $padTicketGrid   = padPrintableGrid ( $padTicketSheets, 'a4-1x4' );
  $padTicketBox    = padPrintableBox ( [ 'pad' => 5, 'size' => 10, 'align' => 'left', 'valign' => 'middle' ] );
  $padTicketNumber = padPrintableInt ( 'number', 1, 0, 9999999 );

  return padPrintablePlan ( [

    'name'  => [ 'ticket', 'tickets' ],
    'grid'  => $padTicketGrid,
    'parts' => [ 'aside' ],

    'item'  => function ( $cell, $parts, $n ) use ( $padTicketBox, $padTicketNumber ) {

      list ( $x, $y, $w, $h ) = $cell;

      if ( $parts === NULL )
        return [ padPrintableEdge ( $cell ) ];

      $stub = round ( min ( 60, max ( 40, $w * 0.28 ) ), 3 );
      $cut  = $x + $w - $stub;
      $text = ( $parts ['aside'] !== '' ) ? $parts ['aside']
            : '<p>No.<br><b>' . str_pad ( (string) ( $padTicketNumber + $n ), 4, '0', STR_PAD_LEFT ) . '</b></p>';

      return [
        [ 'box', $x, $y, $w - $stub, $h, $parts [''], $padTicketBox ],
        [ 'box', $cut, $y, $stub, $h, $text, [ 'pad' => 3.5, 'size' => 8, 'align' => 'center', 'valign' => 'middle' ] ],
        [ 'line', $cut, $y + 2, $cut, $y + $h - 2, [ 'stroke' => '#777777', 'width' => 0.6, 'dash' => TRUE ] ],
        padPrintableEdge ( $cell, TRUE ) ];

    },

  ] );

?>
