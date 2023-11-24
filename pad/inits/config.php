<?php

  include pad . 'config/config.php';

  if ( padExists ( padApp . '_config/config.php' ) ) {
    $padCall = padApp . '_config/config.php';
    include pad . 'call/call.php';
  }

?>