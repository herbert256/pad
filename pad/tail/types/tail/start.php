<?php

  include_once pad . 'tail/types/tail/lib.php';

  if ( $padTailSession )
    padTailSessionStart ();

  if ( $padTailRequest )
    padTailRequestInit ( "$padTailDir/request-entry.json" );

?>