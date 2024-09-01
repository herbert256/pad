<?php

  if ( $padSeqLoop > PHP_INT_MAX )
    return FALSE;
  
  if ( in_array ( $padSeqBuild, ['fixed'] ) )
    padError ( "Sequence operation '$padSeqName' not supported" );

  if     ( $padSeqBuild == 'function'  ) $padRet = ( 'padSeq'          . ucfirst($padSeqSeq) ) ( $padSeqLoop );
  elseif ( $padSeqBuild == 'check'     ) $padRet = ( 'padSeqCheck'     . ucfirst($padSeqSeq) ) ( $padSeqStart, $padSeqLoop );
  elseif ( $padSeqBuild == 'bool'      ) $padRet = ( 'padSeqBool'      . ucfirst($padSeqSeq) ) ( $padSeqLoop );
  else                                   $padRet = include "/pad/sequence/types/$padSeqSeq/$padSeqBuild.php";

  if     ( $padRet === NULL  ) return FALSE;
  elseif ( $padRet === INF   ) return FALSE; 
  elseif ( $padRet === NAN   ) return FALSE; 
  else                         return $padRet;

?>