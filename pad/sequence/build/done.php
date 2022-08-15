<?php

  padDone ( $padSeqSeq,  TRUE );
  padDone ( $padSeqName, TRUE );
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

  foreach ( $padPrmsTag [$pad] as $padSeqTagName => $padSeqTagValue ) {

    if ( file_exists ( PAD . "sequence/types/$padSeqTagName/make.php" ) )
      padDone ( $padSeqTagName, TRUE );

    if ( file_exists ( PAD . "sequence/types/$padSeqTagName/filter.php" ) )
      padDone ( $padSeqTagName, TRUE );

  }

?>