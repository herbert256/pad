<?php

  $padExtPag = $padOpt [$pad] [1] ;
  $padExtQry = ( padTagParm ( 'include' ) ) ? '&padInclude' : '';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

?>