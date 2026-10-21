<?php

  // The extension as a visitor might send it, ?ext=php::$DATA - on Windows the main stream
  // of shell.php, a page the web server runs.

  $_GET ['ext'] = $ext = 'php::$DATA';

?>
