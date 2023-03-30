<?php

  global $padApp, $padPage, $padLogRsl;
  
  padFilePutContents ( "log/$padApp/$padPage/" . padFileTimestamp() . '.json', $padLogRsl );  

?>