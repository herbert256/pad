<?php

  if ( isset ( $_REQUEST ['padInclude']   ) ) return;
  if ( isset ( $_REQUEST ['padExamples']  ) ) return;
  if ( isset ( $_REQUEST ['padReference'] ) ) return;

  include PAD . 'config/tidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE )

    $padOutput = padTidy ( $padOutput );

  elseif ( $padMyTidy )

    include PAD . 'exits/myTidy.php';

?>