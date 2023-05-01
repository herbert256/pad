<?php

  include pad . 'page/pad.php';

  foreach ( $parms as $padK => $padV )
    $$padK = $padV;

  $padRetrieveLevel = $pad;

  include pad . 'level/setup.php'; 

?>