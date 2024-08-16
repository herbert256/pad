<?php
  

  function$GLOBALS ['padInfo']( $dir1, $dir2, $dir3='' ) {

    global $padInfXrefId, $padInfXrefXref, $padInfXrefPage, $padInfXrefXml, $padInfXrefTrace, $padInfXrefTree, $padInfXrefSite;

    $padInfXrefId++;

    if ( $dir2 === FALSE ) $dir2 = '0';
    if ( $dir3 === FALSE ) $dir3 = '0';

    if ( is_array ($dir3) )
      $dir3 = "ARRAY";

    if ( $padInfXrefXref  ) padXrefXref  ( $dir1, $dir2, $dir3, 'xref' );
    if ( $padInfXrefPage  ) padXrefXref  ( $dir1, $dir2, $dir3, 'page' );
    if ( $padInfXrefTree  ) padXrefTree  ( $dir1, $dir2, $dir3 );
    if ( $padInfXrefXml   ) padXrefXml   ( $dir1, $dir2, $dir3 );
    if ( $padInfXrefTrace ) padXrefTrace ( $dir1, $dir2, $dir3 );
 
  }


  function padXrefXref ( $dir1, $dir2, $dir3, $type ) {

    global $padStartPage, $padInfXrefId;

    if ( $type == 'xref' )
      $file = "xref/xref/";
    else
      $file = padXrefLocation () . 'xref/';

    $file .= "$dir1/$dir2/";

    if ( $dir3 !== '' )
      $file .= "$dir3/";

    if ( $type == 'xref' )
      $file .= str_replace ( '/' , '@', $padStartPage ) . "-page";

    padXrefData ( "$file/$padInfXrefId" );

  } 


  function padXrefTree ( $dir1, $dir2, $dir3 ) {

    global $pad, $padLvlIds, $padOccur, $padTag;

    $file = padXrefLocation () . "tree";

    for ( $lvl=0; $lvl<=$pad; $lvl++ ) {
      $l = $padLvlIds [$lvl] ?? 0;
      $o = $padOccur  [$lvl] ?? 0; 
      $t = $padTag    [$lvl] ?? 0;
      $file .= "/L-$l-$t/O-$o";
    }

    $file .= "/X-$dir1/X-$dir2";    
    if ( $dir3 !== '' )
      $file .= "/X-$dir3";

    padXrefData ( $file );

  }


  function padXrefXml ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padInfXrefLevel, $padOccur, $padInfXrefStore;

    $padInfXrefLvl = $padInfXrefLevel [$pad] ?? 0;
    $padInfXrefOcc = $padOccur     [$pad] ?? 0;

    $xref = "$dir1 $dir2";
    if ( $dir3 !== '' )
      $xref .= " $dir3";

    if     ( $padInfXrefOcc == 0)
      $padInfXrefStore [$padInfXrefLvl] ['start'] [] = $xref;
    elseif ( $padInfXrefOcc == 99999)
      $padInfXrefStore [$padInfXrefLvl] ['end']  [] = $xref;
    else
      $padInfXrefStore [$padInfXrefLvl] ['occurs'] [$padInfXrefOcc] ['xref'] [] = $xref;

  }


  function padXrefTrace ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padInfTrace, $padInfTraceLevel;
  
    if ( ! $padInfTrace ) 
      return;
    
    $target = $padInfTraceLevel [$pad] ?? 0;

    if ( $dir3 )
      $xref = "$dir1/dir2/$dir3";
    else
      $xref = "$dir1/$dir2";
  
    padTraceWrite ( $pad, 'xref', $xref );
 
  } 


  function padXrefData ( $file ) {

    global $pad;

    for ( $lvl=$pad; $lvl>=0; $lvl-- ) 
      $data [$lvl] = padDumpGetLevel ($lvl) ;

    padInfoFile ( "$file.json", $data );

  }


  function padXrefLocation () {

    global $padStartPage;

    $dir = "xref/pages/$padStartPage/";

    if ( isset ( $_REQUEST['padInclude'] ) )
      $dir .= "include/";
    else
      $dir .= "complete/";

    return $dir;

  }


  function padXrefXmlMake () {

    global $padInfXrefEvents;

    foreach ( $padInfXrefEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXrefLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXrefLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padXrefOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padXrefOccurEnd   ( $event );

    }

  }


  function padXrefLevelStart ( $event ) {

    global $padInfXrefStore;

    extract ( $event );
    extract ( $padInfXrefStore [$tree]  );

    padXrefOpen ( 'level' );
    padXrefOpen ( 'properties' );
    
    foreach ( $start as $line )
      padXrefParts ( $line );

    padXrefClose ( 'properties' );

    if ( count ($occurs) > 1)
      padXrefOpen ( 'occurs' );

  }


  function padXrefLevelEnd ( $event ) {

    global $padInfXrefStore;

    extract ( $event );
    extract ( $padInfXrefStore [$tree]  );

    if ( count ($occurs) > 1)
      padXrefClose ( 'occurs' );

    if ( count ($end) ) {
      padXrefOpen ( 'result' );
      foreach ( $end as $line )
        padXrefParts ( $line );
      padXrefClose ( 'result' );
    }

    padXrefClose ( 'level' );

  }


  function padXrefOccurStart ( $event ) {

    global $padInfXrefStore;

    extract ( $event );
    extract ( $padInfXrefStore [$tree]  );
    extract ( $occurs [$occur] );

    padXrefOpen ( 'occur' ); 

    foreach ( $xref as $line )
      padXrefParts ( $line );

  }


  function padXrefParts ( $line ) {

    $parts = preg_split ("/[\s]+/", $line, 3, PREG_SPLIT_NO_EMPTY);

    if     ( count ($parts) == 3 ) padXrefWrite ( "<$parts[0] $parts[1]=\"$parts[2]\" />" );
    elseif ( count ($parts) == 2 ) padXrefWrite ( "<$parts[0] value=\"$parts[1]\" />" );
    elseif ( count ($parts) == 1 ) padXrefWrite ( "<$parts[0] />" );

  }


  function padXrefOccurEnd ( $event ) {

    padXrefClose ( 'occur' );

  }


  function padXrefOpen ( $xml ) {
  
    global $padInfXrefDepth;

    padXrefWrite ( "<$xml>" );

    $padInfXrefDepth++;
      
  }


  function padXrefClose ( $xml ) {

    global $padInfXrefDepth;

    $padInfXrefDepth--;
  
    padXrefWrite ( "</$xml>" );
  
  }


  function padXrefWrite ( $xml ) {
  
    global $padInfXrefDepth, $padLog;

    if ( $padInfXrefDepth > 0 )
      $spaces = str_repeat ( ' ', $padInfXrefDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( padXrefLocation () . "xref.xml", "$spaces$xml" );
  
  }

 
?>