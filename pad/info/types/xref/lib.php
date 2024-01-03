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
    if ( $padXrefSite  ) padXrefSite  ( $dir1, $dir2, $dir3 );
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

  
  function padXrefSite ( $dir1, $dir2, $dir3 ) {

    padXrefGo ( $dir1, $dir2, $dir3, 'develop' );
   
    if ( $dir1 == 'tag'        and $dir2 == 'tag'      ) padXrefManual ( 'properties', $dir3 );
    if ( $dir1 == 'field'      and $dir2 == 'tag'      ) padXrefManual ( 'properties', $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'property' ) padXrefManual ( 'properties', $dir3 );
    if ( $dir1 == 'tag'                                ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'constructs'                         ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'options'                            ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                         ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                          ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'    ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'   ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions'  ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'     ) padXrefManual ( $dir1, $dir2, $dir3 );
  
  }


  function padXrefManual ( $dir1, $dir2, $dir3='' ) {

    global $padPage, $padXrefSource, $padStartPage;

    if ( padInsideOther()                             ) return;
    if ( $padPage <> $padStartPage                    ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )          ) return;
    if ( str_contains ( $padStartPage, 'develop'    ) ) return;
    if ( str_contains ( $padStartPage, 'reference'  ) ) return;
    if ( str_contains ( $padStartPage, 'manual'     ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']          ) ) return;

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'  ) return padXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXrefGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXrefSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXrefSource, $dir2 ) === FALSE ) return;
 
    padXrefGo ( $dir1, $dir2, $dir3 );

  }


  function padXrefGo ( $dir1, $dir2, $dir3, $type='manual' ) {

    $file = "_xref/$type/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = padApp . "$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) and in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padInfoLine ( "$file.txt", $page, 1 );

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