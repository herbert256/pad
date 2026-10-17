<?php

  // A drawn grid's band lines without a + are band lines too: = is no cell, so a line of =
  // with spaces and bars is one, and a line of nine dashes is one when the grid has 81
  // cells without such lines and more with them - a compact grid's ---|---|---, as wide as
  // its rows. The first was refused with "it has a '='", the second and third with "it has
  // 99 cells, not 81".

  $rows = [ [ '5 3 . | . 7 . | . . .', '6 . . | 1 9 5 | . . .', '. 9 8 | . . . | . 6 .' ],
            [ '8 . . | . 6 . | . . 3', '4 . . | 8 . 3 | . . 1', '7 . . | . 2 . | . . 6' ],
            [ '. 6 . | . . . | 2 8 .', '. . . | 4 1 9 | . . 5', '. . . | . 8 . | . 7 9' ] ];

  $grid = fn ( $band, $compact ) => implode ( "\n$band\n", array_map (
            fn ( $band ) => implode ( "\n", array_map ( fn ( $row ) => $compact ? str_replace ( ' ', '', $row ) : $row, $band ) ),
            $rows ) );

  $spaced  = $grid ( '= = = | = = = | = = =', FALSE );
  $packed  = $grid ( '===|===|===', TRUE );
  $dashed  = $grid ( '---|---|---', TRUE );

?>
