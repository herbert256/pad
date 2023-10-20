<?php

  $padExtPag = padPageGetName ();
  $padExtQry = ( padTagParm ( 'include' ) ) ? '&padInclude' : '';

  foreach ( $padSetLvl [$pad] as $padK => $padV )
    $padExtQry .= "&$padK=" . urlencode($padV);

?>