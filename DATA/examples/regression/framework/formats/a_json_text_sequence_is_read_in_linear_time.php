<?php

  // 40000 records of a JSON text sequence (2.6 MB): the line of every record was counted from
  // the start of the text again - for a line number used only when the record is bad - so
  // the read took seconds where the same rows as NDJSON take milliseconds.

  $seqRows = [];

  for ( $seqAt = 1; $seqAt <= 40000; $seqAt++ )
    $seqRows [] = [ 'id' => $seqAt, 'name' => "row $seqAt", 'tags' => [ 'a', 'b' ], 'ok' => $seqAt % 2 == 0 ];

  $seqText = padFormatWrite ( 'jsonseq', $seqRows );

  $start   = hrtime ( TRUE );
  $seqBack = padFormatRead ( 'jsonseq', $seqText );
  $seqTime = ( hrtime ( TRUE ) - $start < 1e9 ) ? 'in time' : 'too slow';
  $seqSame = ( $seqBack === $seqRows ) ? 'the same rows' : 'other rows';

?>
