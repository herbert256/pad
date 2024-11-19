<?php

  if ( ! isset ( $padPad ) ) include 'start/pad.php';
  if ( ! isset ( $padApp ) ) include 'start/app.php';
  if ( ! isset ( $padDat ) ) include 'start/data.php';

  define ( 'PAD', $padPad );
  define ( 'APP', $padApp );
  define ( 'DAT', $padDat );

  include PAD . 'pad.php';

?>