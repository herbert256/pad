<?php

  $manualLink = $padParm;
  $manualText = $manualLink;

  if ( isset ( $padOpt [$pad] [2] ) )
    $extra = $padOpt [$pad] [2];
  else
    $extra = '';

  $manualText .= $extra;
  $manualText  = str_replace ( '_', ' ', $manualText);
  $manualText  = ucfirst ( $manualText );

  return 
      '<a href="' 
    .  $padGo 
    . "manual/$manualLink"
    . '">' 
    . $manualText 
    . '</a>';

?>