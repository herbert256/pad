<?php

  global $app, $page, $padLogRsl;
  
  padFilePutContents ( "log/$app/$page/" . padFileTimestamp() . '.json', $padLogRsl );  

?>