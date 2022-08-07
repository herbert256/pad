<?php

  // Callback mode: before

  pCallback_before_xxx ('init');

  foreach ( $pData[$p] as $pK => $pV)
    pCallback_before_row ( $pData[$p] [$pK] );

  pCallback_before_xxx ('exit'); 

?>