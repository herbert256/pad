<?php

  // Settings for $padOutputType 'console', the command line response written by
  // exits/output/console.php. inits/config.php switches to this type automatically when the
  // SAPI is cli. Both HTML clean-up passes are turned off, since terminal output should not
  // be reflowed as HTML.

  $padTidy   = FALSE;
  $padMyTidy = FALSE;

?>