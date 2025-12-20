<?php

  include PAD . 'config/tidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE )

    $padOutput = padTidy ( $padOutput );

  elseif ( $padMyTidy )

    include PAD . 'exits/myTidy.php';

?>
