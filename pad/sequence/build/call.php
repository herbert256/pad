<?php
  
  if     ( $pqBuild == 'function' ) $padRet = ( 'pq'      . ucfirst($pqSeq) ) ( $pqLoop );
  elseif ( $pqBuild == 'bool'     ) $padRet = ( 'pqCheck' . ucfirst($pqSeq) ) ( $pqFrom, $pqLoop, $pqParm );
  elseif ( $pqBuild == 'check'    ) $padRet = ( 'pqCheck' . ucfirst($pqSeq) ) ( $pqFrom, $pqLoop, $pqParm );
  else                              $padRet = include "sequence/types/$pqSeq/$pqBuild.php";

  if     ( $padRet === NULL  ) return FALSE;
  elseif ( $padRet === INF   ) return FALSE; 
  elseif ( $padRet === NAN   ) return FALSE; 
  else                         return $padRet;

?>