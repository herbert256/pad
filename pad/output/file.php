<?php

  if ( $padCacheStop and $padCacheServerGzip )
    $padOutput = padUnzip ( $padOutput ) ;

  $padFile = "$padFileDir/$padFileName";

  if ( $padFileTimeStamp )
    $padFile .= '_' . padTimeStamp ();

  if ( $padFileUniqId )
    $padFile .= '_' . padRandomString ( $padFileUniqId );

  $padFile .= '.' . $padFileExtension;

  padFilePutContents ( $padFile, $padOutput );

  $padSetConfig ['OutputType'] = 'web';

  $padRestart = $padFileNextPage;
  include pad . 'start/restart.php';

?>