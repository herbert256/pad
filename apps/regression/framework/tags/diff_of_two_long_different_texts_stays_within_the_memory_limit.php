<?php

  // Two texts of a thousand words that differ almost everywhere, diffed under a memory
  // limit of 128 MB: the walk kept a copy of its frontier for every distance, and needed
  // 196 MB for these two.

  include_once PAD . 'tags/_lib/diff.php';

  ini_set ( 'memory_limit', '128M' );

  $old = implode ( ' ', array_map ( fn ( $i ) => 'w' . ( ( $i * 7919 )   % 53 ), range ( 1, 1000 ) ) );
  $new = implode ( ' ', array_map ( fn ( $i ) => 'w' . ( ( $i * 104729 ) % 47 ), range ( 1, 1000 ) ) );

  $diff = padDiffWords ( $old, $new );
  $dels = substr_count ( $diff, '<del>' );
  $inss = substr_count ( $diff, '<ins>' );

?>
