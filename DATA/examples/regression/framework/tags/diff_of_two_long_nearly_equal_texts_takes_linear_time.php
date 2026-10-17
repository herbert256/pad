<?php

  // Two texts of 50 000 words, the first and the last word changed: the common start and end
  // were taken off one word at a time with array_shift and array_unshift, each moving the
  // whole rest - eleven seconds, where a few hundredths of one do.

  include_once PAD . 'tags/_lib/diff.php';

  $words = [];
  for ( $i = 0; $i < 50000; $i++ )
    $words [] = "w$i";

  $old   = implode ( ' ', $words );
  $new   = 'first ' . implode ( ' ', array_slice ( $words, 1, -1 ) ) . ' last';
  $start = hrtime ( TRUE );
  $html  = padDiffWords ( $old, $new );
  $time  = ( hrtime ( TRUE ) - $start < 2e9 ) ? 'in time' : 'too slow';
  $marks = substr_count ( $html, '<del' ) . ' deleted, ' . substr_count ( $html, '<ins' ) . ' inserted';

?>
