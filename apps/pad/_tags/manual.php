<?php

  $manualLink = $padOpt [$pad] [1];
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
    . 'manual&manual=' 
    . $manualLink 
    . '&extra=' . urlencode ($extra)
    . '">' 
    . $manualText 
    . '</a>';

?>