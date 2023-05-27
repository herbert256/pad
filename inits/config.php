<?php

  include 'config/config.php';

  if ( padExists ( padApp . '_config/config.php' ) ) {
    $padCall = padApp . '_config/config.php';
    include 'call/call.php';
  }

?>