<?php

  $padBuildBuild = $padPrm [$pad] ['build'] ?? $padPrm [$pad] [1]; 
  $padBuildMode  = $padPrm [$pad] ['mode']  ?? $padPrm [$pad] [2] ?? 'before', 
  $padBuildMerge = $padPrm [$pad] ['merge'] ?? $padPrm [$pad] [3] ?? 'content'

  return padBuild ( $padBuildBuild, $padBuildMode, $padBuildMerge ); 

?>