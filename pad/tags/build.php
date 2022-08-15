<?php

  $pBuild = $pPrmsTag ['build']   ?? $pPrmsVal ['0']; 
  $pMode  = $pPrmsTag ['mode']    ?? $pPrmsVal ['1'] ?? 'before', 
  $pMerge = $pPrmsTag ['merge']   ?? $pPrmsVal ['2'] ?? 'content'

  return pBuild( $pBuild, $pMode, $pMerge); 

?>