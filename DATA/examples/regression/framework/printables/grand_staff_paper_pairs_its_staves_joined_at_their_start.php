<?php

  // With grand the staves come in pairs for the piano, 1.6 staff heights - 11.2 mm at rastral
  // 3 - between the two of a pair, which a line of 0.9 pt joins at their start, and at least
  // 2.2 staff heights between one pair and the next: six pairs on A4, the twelve staves of
  // the paper sold for the piano. With a line over them - the content between the tags - the
  // staves move down 12 mm, the box of the line in the top margin's place; in the PDF the
  // same lines, those of a staff 0.5 pt.

  $answer = staffShow ( '{printable:staffpaper grand/}' ) . ' // '
          . printablesShow ( '{printable:staffpaper grand}<b>Sonatina</b>{/printable:staffpaper}', 2 );

  $pdf = padPdf ( padUnprotect ( padUnescape ( padCode ( '{printable:staffpaper grand/}' ) ) ), [ 'compress' => FALSE ] );

  $answer .= ' // pdf: ' . implode ( ', ', array_map ( fn ( $w, $n ) => "$n lines of $w pt",
                                                       array_keys ( $c = array_count_values ( preg_match_all ( '/RG ([0-9.]+) w [0-9. ]+ m [0-9. ]+ l S Q/', $pdf, $m ) ? $m [1] : [] ) ), $c ) );

?>
