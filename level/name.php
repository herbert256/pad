<?php

  if ( isset ( $padPrm [$pad] ['name'] ) )
    $padName [$pad] = $padPrm [$pad] ['name'];
  elseif ( $padForceName )
    $padName [$pad] = $padForceName;
  else
    $padName [$pad] = $padTag [$pad];
  
?>