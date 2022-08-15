<?php

  padDone ( $padSeq_seq,  TRUE );
  padDone ( $padSeq_name, TRUE );
  padDone ( 'from',        TRUE );
  padDone ( 'increment',   TRUE );
  padDone ( 'to',          TRUE );
  padDone ( 'rows',        TRUE );
  padDone ( 'min',         TRUE );
  padDone ( 'max',         TRUE );
  padDone ( 'unique',      TRUE );
  padDone ( 'page',        TRUE );
  padDone ( 'start',       TRUE );
  padDone ( 'end',         TRUE );
  padDone ( 'low',         TRUE );
  padDone ( 'high',        TRUE );
  padDone ( 'random',      TRUE );
  padDone ( 'push',        TRUE );
  padDone ( 'store',       TRUE );
  padDone ( 'pull',        TRUE );
  padDone ( 'sequence',    TRUE );
  padDone ( 'range',       TRUE );
  padDone ( 'protect',     TRUE );
  padDone ( 'keep',        TRUE );
  padDone ( 'remove',      TRUE );
  padDone ( 'make',        TRUE );

  foreach ( $padPrmsTag [$pad] as $padSeq_tag_name => $padSeq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$padSeq_tag_name/make.php" ) )
      padDone ( $padSeq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$padSeq_tag_name/filter.php" ) )
      padDone ( $padSeq_tag_name, TRUE );

  }

?>