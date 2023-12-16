<?php
  

  function padXref ( $dir1, $dir2, $dir3='' ) {

    global $padXrefId;
    global $padXrefInfo, $padXrefTypes, $padXrefXml, $padXrefTrace, $padXrefPage, $padXrefManual;

    $padXrefId++;

    if ( $dir2 === FALSE ) $dir2 = '0';
    if ( $dir3 === FALSE ) $dir3 = '0';

    padInfo ( 'xref', $dir1, $dir2, $dir3 );

    if ( $padXrefInfo    ) padXrefInfo    ( $dir1, $dir2, $dir3 );
    if ( $padXrefManual  ) padXrefManual  ( $dir1, $dir2, $dir3 );
    if ( $padXrefTypes   ) padXrefTypes   ( $dir1, $dir2, $dir3 );
    if ( $padXrefPage    ) padXrefPage    ( $dir1, $dir2, $dir3 );
    if ( $padXrefXml     ) padXrefXml     ( $dir1, $dir2, $dir3 );
    if ( $padXrefTrace   ) padXrefTrace   ( $dir1, $dir2, $dir3 );
 
  }

  
  function padXrefManual ( $dir1, $dir2, $dir3 ) {
   
    if ( $dir1 == 'tag'       and $dir2 == 'tag'       ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'field'     and $dir2 == 'tag'       ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'property' ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'tag'                                ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'constructs'                         ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'parms'      and $dir2 == 'options'  ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                         ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                          ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'    ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions'  ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'     ) padXrefManual2 ( $dir1, $dir2, $dir3 );
  
  }


  function padXrefManual2 ( $dir1, $dir2, $dir3='' ) {

    global $padPage, $padXrefPageSource, $padStartPage;

    if ( padInsideOther()                             ) return;
    if ( $padPage <> $padStartPage                    ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )          ) return;
    if ( str_contains ( $padStartPage, 'develop/'   ) ) return;
    if ( str_contains ( $padStartPage, 'reference/' ) ) return;
    if ( str_contains ( $padStartPage, 'manual/'    ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']          ) ) return;

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXrefManual3 ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXrefPageSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXrefPageSource, $dir2 ) === FALSE ) return;
 
    padXrefManual3 ( $dir1, $dir2, $dir3 );

  }


  function padXrefManual3 ( $dir1, $dir2, $dir3 ) {

    $explode1 = padExplode ( $dir2, ':' );
    $explode2 = padExplode ( $dir3, ':' );

    if ( $dir2 and $dir3 )
      foreach ($explode1 as $value1)
        foreach ($explode2 as $value2)
          if ( ctype_alpha ( $value1 ) and ctype_alpha ( $value2) )
            padXrefManual4 ( padApp . '_xref', $dir1, $value1, $value2 );

    if ( $dir2 and ! $dir3 )
      foreach ($explode1 as $value1)
          if ( ctype_alpha ( $value1 ) )
            padXrefManual4 ( padApp . '_xref', $dir1, $value1, $dir3 );

  }


  function padXrefManual4 ( $dir, $dir1, $dir2, $dir3 ) {

    global $padStartPage;

    $dir .= "/$dir1/$dir2";

    if ( $dir3 !== '' )
      $dir .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    if ( ! file_exists ( $dir ) )
      mkdir ( $dir, 0777, TRUE );

    $file = "$dir/" .  str_replace ( '/' , '@', padFileCorrect ($padStartPage ) ) . '.hit';

    if ( ! file_exists ( $file ) )
      touch ( $file );

  }


  function padXrefXml ( $dir1, $dir2, $dir3 ) { 

    global $pad, $padXrefLevel, $padOccur, $padXrefTree;

    $padXrefLvl = $padXrefLevel [$pad] ?? 0;
    $padXrefOcc = $padOccur     [$pad] ?? 0;

    if ( is_array ($dir3) )
      $dir3 = "ARRAY";

    $xref = "$dir1 $dir2";
    if ( $dir3 !== '' )
      $xref .= " $dir3";

    if     ( $padXrefOcc == 0)
      $padXrefTree [$padXrefLvl] ['start'] [] = $xref;
    elseif ( $padXrefOcc == 99999)
      $padXrefTree [$padXrefLvl] ['end']  [] = $xref;
    else
      $padXrefTree [$padXrefLvl] ['occurs'] [$padXrefOcc] ['xref'] [] = $xref;

  }


  function padXrefTypes ( $dir1, $dir2, $dir3 ) {

    global $pad, $padStartPage, $padLvlId, $padXrefFile;

    $file = "develop/xref/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $file .= "/" .  str_replace ( '/' , '@', padFileCorrect ($padStartPage ) ) . "-hit/$padLvlId.json";

    if ( file_exists ( padData . $file ) )
      return;

    for ( $lvl=$pad; $lvl>=0; $lvl-- ) 
      $data [$lvl] = padDumpGetLevel ($lvl) ;

    padInfoFile ( $file, $data );

  }


  function padXrefPage ( $dir1, $dir2, $dir3 ) {

    global $pad, $padPad, $padLvlIds, $padOccur;
    global $padInfoPage, $padCurrent, $padBase, $padData, $padResult;

    $file = "$padInfoPage/tree";

    for ( $lvl=0; $lvl<=$pad; $lvl++ ) {
      $l = $padLvlIds [$pad] ?? 0;
      $o = $padOccur  [$pad] ?? 0;
      if ( $lvl > 0 ) 
        $file .= "/lvl/$l/occ";
      $file .= "/$o";
    }

    $file .= "/$dir1/$dir2";    
    if ( $dir3 !== '' )
      $file .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $file .= ".json";

    if ( file_exists ( padData . $file ) )
      return;

    $data = [
      'now-data'    => $padCurrent [$pad],
      'now-content' => $padPad     [$pad],
      'data'        => $padData    [$pad],
      'base'        => $padBase    [$pad],
      'result'      => $padResult  [$pad]
    ];

    padInfoFile ( $file, $data );

  }


  function padXrefInfo ( $dir1, $dir2, $dir3 ) {

    padXrefLine ( 'xref', $dir1, $dir2, $dir3 );

  }


  function padXrefTrace ( $dir1, $dir2, $dir3 ) {

    global $pad, $padTrace, $padTraceLevel;
  
    if ( ! $padTrace ) 
      return;
    
    $target = $padTraceLevel [$pad] ?? 0;
  
    padXrefLine ( "trace/$target/xref", $dir1, $dir2, $dir3 );
 
  } 


  function padXrefLine ( $target, $dir1, $dir2, $dir3 ) {

    global $padInfoDir;
  
    if ( $dir3 )
      $xref = "$dir2/" . padFileCorrect ($dir3 );
    else
      $xref = padFileCorrect ($dir2 );
  
    padInfoLine ( "$padInfoDir/$target/$dir1/$xref.txt", padInfoIds () );

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

    global $padXrefTree;

    extract ( $event );
    extract ( $padXrefTree [$tree]  );

    padXrefOpen ( 'level' );
    padXrefOpen ( 'properties' );
    
    foreach ( $start as $line )
      padXrefParts ( $line );

    padXrefClose ( 'properties' );

    if ( count ($occurs) > 1)
      padXrefOpen ( 'occurs' );

  }


  function padXrefLevelEnd ( $event ) {

    global $padXrefTree;

    extract ( $event );
    extract ( $padXrefTree [$tree]  );

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

    global $padXrefTree;

    extract ( $event );
    extract ( $padXrefTree [$tree]  );
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
  
    global $padInfoPage, $padXrefDepth;

    if ( $padXrefDepth > 0 )
      $spaces = str_repeat ( ' ', $padXrefDepth * 2 );
    else
      $spaces = '';

    padInfoLine ( "$padInfoPage/xref.xml", "$spaces$xml", true );
  
  }

 
?>