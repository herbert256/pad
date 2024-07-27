<?php
  

  function padXref ( $dir1, $dir2, $dir3='' ) {

    global $padXrefId, $padXrefXref, $padXrefPage, $padXrefXml, $padXrefTrace, $padXrefTree, $padXrefSite;

    $padXrefId++;

    if ( $dir2 === FALSE ) $dir2 = '0';
    if ( $dir3 === FALSE ) $dir3 = '0';

    if ( is_array ($dir3) )
      $dir3 = "ARRAY";

    if ( $padXrefXref  ) padXrefXref  ( $dir1, $dir2, $dir3, 'xref' );
    if ( $padXrefPage  ) padXrefXref  ( $dir1, $dir2, $dir3, 'page' );
    if ( $padXrefTree  ) padXrefTree  ( $dir1, $dir2, $dir3 );
    if ( $padXrefXml   ) padXrefXml   ( $dir1, $dir2, $dir3 );
    if ( $padXrefTrace ) padXrefTrace ( $dir1, $dir2, $dir3 );
 
  }


  function padXrefXref ( $dir1, $dir2, $dir3, $type ) {

    global $padStartPage, $padXrefId;

    if ( $type == 'xref' )
      $file = "xref/xref/";
    else
      $file = padXrefLocation () . 'xref/';

    $file .= "$dir1/$dir2/";

    if ( $dir3 !== '' )
      $file .= "$dir3/";

    if ( $type == 'xref' )
      $file .= str_replace ( '/' , '@', $padStartPage ) . "-page";

    padXrefData ( "$file/$padXrefId" );

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

    global $pad, $padXrefLevel, $padOccur, $padXrefStore;

    $padXrefLvl = $padXrefLevel [$pad] ?? 0;
    $padXrefOcc = $padOccur     [$pad] ?? 0;

    $xref = "$dir1 $dir2";
    if ( $dir3 !== '' )
      $xref .= " $dir3";

    if     ( $padXrefOcc == 0)
      $padXrefStore [$padXrefLvl] ['start'] [] = $xref;
    elseif ( $padXrefOcc == 99999)
      $padXrefStore [$padXrefLvl] ['end']  [] = $xref;
    else
      $padXrefStore [$padXrefLvl] ['occurs'] [$padXrefOcc] ['xref'] [] = $xref;

  }


  function padXrefTrace ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padTrace, $padTraceLevel;
  
    if ( ! $padTrace ) 
      return;
    
    $target = $padTraceLevel [$pad] ?? 0;

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

    global $padXrefEvents;

    foreach ( $padXrefEvents as $key => $event ) {

      if     ( $event ['event'] == 'level-start' ) padXrefLevelStart ( $event );
      elseif ( $event ['event'] == 'level-end'   ) padXrefLevelEnd   ( $event );
      elseif ( $event ['event'] == 'occur-start' ) padXrefOccurStart ( $event );
      elseif ( $event ['event'] == 'occur-end'   ) padXrefOccurEnd   ( $event );

    }

  }


  function padXrefLevelStart ( $event ) {

    global $padXrefStore;

    extract ( $event );
    extract ( $padXrefStore [$tree]  );

    padXrefOpen ( 'level' );
    padXrefOpen ( 'properties' );
    
    foreach ( $start as $line )
      padXrefParts ( $line );

    padXrefClose ( 'properties' );

    if ( count ($occurs) > 1)
      padXrefOpen ( 'occurs' );

  }


  function padXrefLevelEnd ( $event ) {

    global $padXrefStore;

    extract ( $event );
    extract ( $padXrefStore [$tree]  );

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

    global $padXrefStore;

    extract ( $event );
    extract ( $padXrefStore [$tree]  );
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
  
    global $padXrefDepth;

    padXrefWrite ( "<$xml>" );

    $padXrefDepth++;
      
  }


  function padXrefClose ( $xml ) {

    global $padXrefDepth;

    $padXrefDepth--;
  
    padXrefWrite ( "</$xml>" );
  
  }


  function padXrefWrite ( $xml ) {
  
    global $padXrefDepth, $padLog;

    if ( $padXrefDepth > 0 )
      $spaces = str_repeat ( ' ', $padXrefDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( padXrefLocation () . "xref.xml", "$spaces$xml" );
  
  }

 
?>