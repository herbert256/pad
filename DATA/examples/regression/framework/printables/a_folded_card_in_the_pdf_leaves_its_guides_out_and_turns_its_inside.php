<?php

  // In the PDF a folded card is its content alone: the folds and the names of the panels are
  // the screen's (data-pdf-skip), and so not in the document - its four texts, no line - and
  // the two panels of the inside are turned half round their middles (-1 0 0 -1), the front
  // and the back not. With folds the folds are printed too, two light lines of 0.3 pt, which
  // end up on the edges of the card.

  function foldcardPdf ( $template ) {

    $pdf = padPdf ( padUnprotect ( padUnescape ( padCode ( $template ) ) ), [ 'compress' => FALSE ] );

    preg_match_all ( '/\((Front|Back|Left|Right|Inside left|Inside right)\) Tj/', $pdf, $texts );
    preg_match_all ( '/re W n -1 0 0 -1/', $pdf, $turns );
    preg_match_all ( '/q ([0-9. ]+) RG ([0-9.]+) w ([0-9. ]+) m ([0-9. ]+) l S Q/', $pdf, $lines, PREG_SET_ORDER );

    return implode ( ', ', $texts [1] ) . ' // ' . count ( $turns [0] ) . ' turned // '
         . ( $lines ? implode ( ', ', array_map ( fn ( $l ) => "line {$l [3]} to {$l [4]} of {$l [2]} pt", $lines ) ) : 'no lines' );

  }

  $card   = '<header>Front</header><aside>Left</aside><p>Right</p><footer>Back</footer>';
  $answer = foldcardPdf ( "{printable:foldcard}$card{/printable:foldcard}" ) . ' ## ' . foldcardPdf ( "{printable:foldcard folds}$card{/printable:foldcard}" );

?>
