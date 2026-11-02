<?php

  // A box of a printable in a PDF: its content set inside a clip of the box, moved down to its
  // middle when valign is middle, and for the upper half of a place card turned half round its
  // own middle - the matrix -1 0 0 -1 with twice the middle as its move - so that, folded, the
  // card reads from both sides. The two halves of an A4 quarter: 297.64 by 210.47 points, the
  // upper one from 841.89 down to 631.42, its middle at 148.82, 736.66.

  $html = padUnprotect ( padUnescape ( padCode ( "{printable:placecards}<h2>Ann</h2>{/printable:placecards}" ) ) );
  $pdf  = padPdf ( $html, [ 'compress' => FALSE ] );

  preg_match_all ( '/q ([-0-9. ]+) re W n((?: [-0-9. ]+ cm)*)/', $pdf, $clips, PREG_SET_ORDER );

  $answer = implode ( ' // ', array_map ( fn ( $one ) => 'clip ' . $one [1] . ( trim ( $one [2] ) !== '' ? ',' . $one [2] : '' ), $clips ) );

?>
