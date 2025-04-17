<?php
  
  if     ( $pqBuild == 'function' ) $padRet = ( 'pq'      . ucfirst($pqSeq) ) ( $pqLoop );
  elseif ( $pqBuild == 'check'    ) $padRet = ( 'pqCheck' . ucfirst($pqSeq) ) ( $pqStart, $pqLoop, $pqParm );
  elseif ( $pqBuild == 'bool'     ) $padRet = ( 'pqBool'  . ucfirst($pqSeq) ) ( $pqLoop );
  else                                  $padRet = include "sequence/types/$pqSeq/$pqBuild.php";

  if     ( $padRet === NULL  ) return FALSE;
  elseif ( $padRet === INF   ) return FALSE; 
  elseif ( $padRet === NAN   ) return FALSE; 
  else                         return $padRet;

?>