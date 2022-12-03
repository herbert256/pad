<?php

  $padBuildBuild = $padPrmsTag [$pad] ['build'] ?? $padPrmsVal [$pad] ['0']; 
  $padBuildMode  = $padPrmsTag [$pad] ['mode']  ?? $padPrmsVal [$pad] ['1'] ?? 'before', 
  $padBuildMerge = $padPrmsTag [$pad] ['merge'] ?? $padPrmsVal [$pad] ['2'] ?? 'content'

  return padBuild ( $padBuildBuild, $padBuildMode, $padBuildMerge ); 

?>