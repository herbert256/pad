<?php

  if ( isset ( $padPrm [$pad] ['name'] ) )
    $padName [$pad] = $padPrm [$pad] ['name'];
  elseif ( $padSetName )
    $padName [$pad] = $padSetName;
  else
    $padName [$pad] = $padTag [$pad];
  
?>