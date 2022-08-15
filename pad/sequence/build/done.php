<?php

  pDone ( $padSeq_seq,  TRUE );
  pDone ( $padSeq_name, TRUE );
  pDone ( 'from',        TRUE );
  pDone ( 'increment',   TRUE );
  pDone ( 'to',          TRUE );
  pDone ( 'rows',        TRUE );
  pDone ( 'min',         TRUE );
  pDone ( 'max',         TRUE );
  pDone ( 'unique',      TRUE );
  pDone ( 'page',        TRUE );
  pDone ( 'start',       TRUE );
  pDone ( 'end',         TRUE );
  pDone ( 'low',         TRUE );
  pDone ( 'high',        TRUE );
  pDone ( 'random',      TRUE );
  pDone ( 'push',        TRUE );
  pDone ( 'store',       TRUE );
  pDone ( 'pull',        TRUE );
  pDone ( 'sequence',    TRUE );
  pDone ( 'range',       TRUE );
  pDone ( 'protect',     TRUE );
  pDone ( 'keep',        TRUE );
  pDone ( 'remove',      TRUE );
  pDone ( 'make',        TRUE );

  foreach ( $padPrmsTag [$pad] as $padSeq_tag_name => $padSeq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$padSeq_tag_name/make.php" ) )
      pDone ( $padSeq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$padSeq_tag_name/filter.php" ) )
      pDone ( $padSeq_tag_name, TRUE );

  }

?>