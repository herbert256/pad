<?php

  padTimingStart ('build');

  $padLib = padApp . 'lib'; 
  include 'lib.php';

  include pad . 'build/build.php';

  if ( $padTrace )
    padFilePutContents ("$padTraceDir/base.html", $padBase [$pad] );

  padTimingEnd ('build');

?>