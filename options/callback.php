<?php

  if ( $GLOBALS['padParse'] )
    return;
  
  $padCall = padApp . "callbacks/" . $GLOBALS ['padPrm'] [$GLOBALS ['pad']] ['callback'];

  include pad . 'build/go.php';

?>