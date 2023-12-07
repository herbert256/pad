<?php

  include_once pad . 'info/types/main/lib.php';

  if ( $padMainSession )
    padMainSessionStart ();

  if ( $padMainRequest )
    padMainRequestInit ( "$padInfoDir/request-entry.json" );

?>