<?php

  $padBuild = $padPrmsTag ['build'] ?? $padPrmsVal ['0']; 
  $padMode  = $padPrmsTag ['mode']  ?? $padPrmsVal ['1'] ?? 'before', 
  $padMerge = $padPrmsTag ['merge'] ?? $padPrmsVal ['2'] ?? 'content'

  return padBuild ( $padBuild, $padMode, $padMerge ); 

?>