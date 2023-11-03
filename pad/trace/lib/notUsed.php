<?php


  function padTraceFileX ( $type, $data ) {

    global $pad, $padOccur;
    global $padTraceBase, $padTraceLevelDir, $padTraceOccurlDir;

    if ( $pad < 0 )
      return padTraceFileBase ( $type, $data )

    $occur = $padOccur [$pad];

    if ( ! $occur or ! $padTraceOccurlDir [$pad] [$occur] ) 
      padTraceFileLevel ( $type, $data )
    else
      padTraceFileOccur ( $type, $data );

  }


  function padTraceFileBase ( $type, $data ) {

    global $padTraceBase;

    padTraceFileGo ( $padTraceBase, $type, $data ) {  

  }  


  function padTraceFileLevel ( $type, $data ) {

    global $pad, $padTraceLevelDir;

    padTraceFileGo ( $padTraceLevelDir [$pad], $type, $data ) {  

  }  


  function padTraceFileOccur ( $type, $data ) {

    global $pad, $padOccur, $padTraceLevelDir, $padTraceOccurDir;

    $occur = $padOccur [$pad];

    padTraceFileGo ( $padTraceOccurDir [$pad] [$occur], $type, $data ) {  

  }  


  function padTraceFileGo ( $location, $type, $data ) {  

    global $padTraceLine, $padTraceActive, $padTraceFiles, $padTraceFilesDir;

    if ( ! $location or ! $padTraceFiles )
      return;

    if ( is_array ( $data ) )
      $extension = 'json';
    else
      $extension = 'txt';

    $padTraceActive = FALSE;

    if ( $padTraceFilesDir )
      padFilePutContents ( "$location/files/$padTraceLine-$type.$extension", $data );
    else
      padFilePutContents ( "$location/$padTraceLine-$type.$extension", $data );
    
    $padTraceActive = TRUE;

  }

  function padTraceCheck ( $type, $event, $data ) {

    $check = ucwords ($event);

    if ( $GLOBALS ["padTrace$check"] )
      padTraceLineFile ( $type, $event, $data );

  }


  function padTraceLineFile ( $type, $event, $data ) {

    if ( is_array ($data) )
      $data = padJson ( $data )

    padTrace ( $type, $event, $data );

    if ( strlen ( $data ) > 50 )
      if     ( $type == 'level' ) padTraceFileLevel ( $event, $data ); 
      elseif ( $type == 'occur' ) padTraceFileOccur ( $event, $data ); 
      else                        padTraceFileBase  ( $event, $data ); 

  }


?>