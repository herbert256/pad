<?php

  // Callback mode: before

  pCallback_before_xxx ('init');

  foreach ( $pData [$pad] as $pK => $pad_v)
    pCallback_before_row ( $pData [$pad] [$pK] );

  pCallback_before_xxx ('exit'); 

?>