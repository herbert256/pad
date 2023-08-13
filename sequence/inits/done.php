<?php

  padDone ( $padSeqSeq,    TRUE );
  padDone ( $padSeqName,   TRUE );
  padDone ( 'increment',   TRUE );
  padDone ( 'rows',        TRUE );
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
  padDone ( 'from',        TRUE );
  padDone ( 'to',          TRUE );
  padDone ( 'min',         TRUE );
  padDone ( 'max',         TRUE );
  padDone ( 'unique',      TRUE );
  padDone ( 'page',        TRUE );
 
  foreach ( $padPrm [$pad] as $padSeqTagName => $padSeqTagValue )
    if ( padSeqMakeType ("$padSeqTypes/$padSeqTagName/") ) 
      padDone ( $padSeqTagName, TRUE );

?>