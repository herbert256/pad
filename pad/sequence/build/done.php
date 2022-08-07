<?php

  pDone ( $pSeq_seq,  TRUE );
  pDone ( $pSeq_name, TRUE );
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

  foreach ( $pPrmsTag [$p] as $pSeq_tag_name => $pSeq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$pSeq_tag_name/make.php" ) )
      pDone ( $pSeq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$pSeq_tag_name/filter.php" ) )
      pDone ( $pSeq_tag_name, TRUE );

  }

?>