<?php
    
  $padBtwCnt++;

  $padWords         = preg_split("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);
  $padTag [$pad]      = trim($padWords[0] ?? '');
  $padPrms [$pad]     = trim($padWords[1] ?? '');
  $padPrmsType [$pad] = ( $padPrms [$pad]) ? 'open' : 'none';

  if ( $padTrace ) {
    $padBetweenTrace = ['between' => $padBetween, 'tag' => $padTag[$padP], 'prms' => $padPrms[$padP] ];
    pFile_put_contents ( $padLevelDir [$padP] . "/between/" . $padBtwCnt . ".json", $padBetweenTrace ); 
  }

?>