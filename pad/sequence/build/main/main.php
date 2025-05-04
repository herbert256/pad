<?php

  $pqRet = include "sequence/build/main/$pqBuild.php";

  if     ( $pqRet === NULL ) return FALSE;
  elseif ( $pqRet === INF  ) return FALSE; 
  elseif ( $pqRet === NAN  ) return FALSE; 
  else                       return $pqRet;

?>