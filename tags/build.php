<?php

  $padBuildBuild = $padPrm [$pad] ['build'] ?? $padOpt [$pad] [1]; 
  $padBuildMode  = $padPrm [$pad] ['mode']  ?? $padOpt [$pad] [2] ?? 'before', 
  $padBuildMerge = $padPrm [$pad] ['merge'] ?? $padOpt [$pad] [3] ?? 'content'

  return padBuild ( $padBuildBuild, $padBuildMode, $padBuildMerge ); 

?>