<?php

  if ( $padName [$pad] )
    return;
  
  if ( isset ( $padPrm [$pad] ['name'] ) )
    $padName [$pad] = $padPrm [$pad] ['name'];
  elseif ( $padForceTagName )
    $padName [$pad] = $padForceTagName;
  else
    $padName [$pad] = $padTag [$pad];
  
?>