<?php
  

  function padInfoXref ( $dir1, $dir2, $dir3='' ) {

    global $padInfoXrefId, $padInfoXrefXref, $padInfoXrefPage, $padInfoXrefXml, $padInfoXrefTrace, $padInfoXrefTree, $padInfoXrefSite, $padInfoXrefXapp;

    $padInfoXrefId++;

    if ( $dir2 === FALSE ) $dir2 = '0';
    if ( $dir3 === FALSE ) $dir3 = '0';

    if ( is_array ($dir3) )
      $dir3 = "ARRAY";

    if ( $padInfoXrefXref  ) padInfoXrefXref  ( $dir1, $dir2, $dir3, 'xref' );
    if ( $padInfoXrefPage  ) padInfoXrefXref  ( $dir1, $dir2, $dir3, 'page' );
    if ( $padInfoXrefTree  ) padInfoXrefTree  ( $dir1, $dir2, $dir3 );
    if ( $padInfoXrefXml   ) padInfoXrefXml   ( $dir1, $dir2, $dir3 );
    if ( $padInfoXrefTrace ) padInfoXrefTrace ( $dir1, $dir2, $dir3 );
    if ( $padInfoXrefXapp  ) padInfoXrefXapp  ( $dir1, $dir2, $dir3 );
 
  }


  function padInfoXrefXapp ( $dir1, $dir2, $dir3 ) {

    padInfoXappGo ( '_xref/_xref', $dir1, $dir2, $dir3 );

  }


  function padInfoXrefXref ( $dir1, $dir2, $dir3, $type ) {

    global $padStartPage, $padInfoXrefId;

    if ( $type == 'xref' )
      $file = "xref/xref/";
    else
      $file = padInfoXrefLocation () . 'xref/';

    $file .= "$dir1/$dir2/";

    if ( $dir3 !== '' )
      $file .= "$dir3/";

    if ( $type == 'xref' )
      $file .= str_replace ( '/' , '_', $padStartPage );

    padInfoXrefData ( "$file/$padInfoXrefId" );

  } 


  function padInfoXrefTree ( $dir1, $dir2, $dir3 ) {

    global $pad, $padLvlIds, $padOccur, $padTag;

    $file = padInfoXrefLocation () . "tree";

    for ( $lvl=0; $lvl<=$pad; $lvl++ ) {
      $l = $padLvlIds [$lvl] ?? 0;
      $o = $padOccur  [$lvl] ?? 0; 
      $t = $padTag    [$lvl] ?? 0;
      $file .= "/L-$l-$t/O-$o";
    }

    $file .= "/X-$dir1/X-$dir2";    
    if ( $dir3 !== '' )
      $file .= "/X-$dir3";

    padInfoXrefData ( $file );

  }


  function padInfoXrefXml ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padInfoXrefLevel, $padOccur, $padInfoXrefStore;

    $padInfoXrefLvl = $padInfoXrefLevel [$pad] ?? 0;
    $padInfoXrefOcc = $padOccur         [$pad] ?? 0;

    $xref = "$dir1 $dir2";
    if ( $dir3 !== '' )
      $xref .= " $dir3";

    if     ( $padInfoXrefOcc == 0)
      $padInfoXrefStore [$padInfoXrefLvl] ['start'] [] = $xref;
    elseif ( $padInfoXrefOcc == 99999)
      $padInfoXrefStore [$padInfoXrefLvl] ['end']  [] = $xref;
    else
      $padInfoXrefStore [$padInfoXrefLvl] ['occurs'] [$padInfoXrefOcc] ['xref'] [] = $xref;

  }


  function padInfoXrefTrace ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padInfoTrace;
  
    if ( ! $padInfoTrace ) 
      return;
    
    if ( $dir3 )
      $xref = "$dir1/$dir2/$dir3";
    else
      $xref = "$dir1/$dir2";
  
    padInfoTraceWrite ( $pad, 'xref', $xref );
 
  } 


  function padInfoXrefData ( $file ) {

    global $pad;

    for ( $lvl=$pad; $lvl>=0; $lvl-- ) 
      $data [$lvl] = padDumpGetLevel ($lvl) ;

    padInfoFile ( "$file.json", $data );

  }


  function padInfoXrefLocation () {

    global $padStartPage;

    $dir = "xref/pages/$padStartPage/";

    if ( isset ( $_REQUEST['padInclude'] ) )
      $dir .= "include/";
    else
      $dir .= "complete/";

    return $dir;

  }


  function padInfoXrefXmlMake () {

    global $padInfoXrefEvents;

    foreach ( $padInfoXrefEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padInfoXrefLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padInfoXrefLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padInfoXrefOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padInfoXrefOccurEnd   ( $event );

    }

  }


  function padInfoXrefLevelStart ( $event ) {

    global $padInfoXrefStore;

    extract ( $event );
    extract ( $padInfoXrefStore [$tree]  );

    padInfoXrefOpen ( 'level' );
    padInfoXrefOpen ( 'properties' );
    
    foreach ( $start as $line )
      padInfoXrefParts ( $line );

    padInfoXrefClose ( 'properties' );

    if ( count ($occurs) > 1)
      padInfoXrefOpen ( 'occurs' );

  }


  function padInfoXrefLevelEnd ( $event ) {

    global $padInfoXrefStore;

    extract ( $event );
    extract ( $padInfoXrefStore [$tree]  );

    if ( count ($occurs) > 1)
      padInfoXrefClose ( 'occurs' );

    if ( count ($end) ) {
      padInfoXrefOpen ( 'result' );
      foreach ( $end as $line )
        padInfoXrefParts ( $line );
      padInfoXrefClose ( 'result' );
    }

    padInfoXrefClose ( 'level' );

  }


  function padInfoXrefOccurStart ( $event ) {

    global $padInfoXrefStore;

    extract ( $event );
    extract ( $padInfoXrefStore [$tree]  );
    extract ( $occurs [$occur] );

    padInfoXrefOpen ( 'occur' ); 

    foreach ( $xref as $line )
      padInfoXrefParts ( $line );

  }


  function padInfoXrefParts ( $line ) {

    $parts = preg_split ("/[\s]+/", $line, 3, PREG_SPLIT_NO_EMPTY);

    if     ( count ($parts) == 3 ) padInfoXrefWrite ( "<$parts[0] $parts[1]=\"$parts[2]\" />" );
    elseif ( count ($parts) == 2 ) padInfoXrefWrite ( "<$parts[0] value=\"$parts[1]\" />" );
    elseif ( count ($parts) == 1 ) padInfoXrefWrite ( "<$parts[0] />" );

  }


  function padInfoXrefOccurEnd ( $event ) {

    padInfoXrefClose ( 'occur' );

  }


  function padInfoXrefOpen ( $xml ) {
  
    global $padInfoXrefDepth;

    padInfoXrefWrite ( "<$xml>" );

    $padInfoXrefDepth++;
      
  }


  function padInfoXrefClose ( $xml ) {

    global $padInfoXrefDepth;

    $padInfoXrefDepth--;
  
    padInfoXrefWrite ( "</$xml>" );
  
  }


  function padInfoXrefWrite ( $xml ) {
  
    global $padInfoXrefDepth, $padLog;

    if ( $padInfoXrefDepth > 0 )
      $spaces = str_repeat ( ' ', $padInfoXrefDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( padInfoXrefLocation () . "xref.xml", "$spaces$xml" );
  
  }

 
?>