<?php

  padDeleteDataDir ( DAT . 'reference'  );

  set_time_limit ( 60 );

  foreach ( padAppsList () as $one ) {

    extract ( $one );

    $curl = padCurl ( "$padHost/$app?$item&padReference" );

  }

?>