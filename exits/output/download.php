<?php

  $padFile = padFileName ( FALSE );

  padDownLoadHeaders ( $padContentType, $padFile, $padLen );

  echo $padOutput;

  padExit ();

?>