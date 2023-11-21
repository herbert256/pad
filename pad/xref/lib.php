<?php


  function padXref ( $dir1, $dir2, $dir3='' ) {

    global $padXrefDevelop, $padXrefReverse, $padXml, $padXmlXref, $padTrace, $padTraceXref;

    if ( $padXrefReverse ) padXrefReverse ( $dir1, $dir2, $dir3 );
    if ( $padXrefDevelop ) padXrefDevelop ( $dir1, $dir2, $dir3 );

    if ( $padXml   and $padXmlXref   ) padXrefXml   ( $dir1, $dir2, $dir3 );
    if ( $padTrace and $padTraceXref ) padXrefTrace ( $dir1, $dir2, $dir3 );

    if ( $dir1 == 'tags'   and $dir2 == 'tag'         ) padXref ( 'properties', $dir3 );
    if ( $dir1 == 'fields' and $dir2 == 'tag'         ) padXref ( 'properties', $dir3 );
    if ( $dir1 == 'at'     and $dir2 == 'property'    ) padXref ( 'properties', $dir3 );

    if ( $dir1 == 'tags'                              ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'constructs'                        ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'parms'      and $dir2 == 'options' ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                        ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                         ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'   ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) padXrefManual ( $dir1, $dir2, $dir3 );

  }
  

  function padXrefManual ( $dir1, $dir2, $dir3 ) {

    global $padPage, $padXrefPageSouce, $padXrefManual, $padStartPage;

    if ( ! $padXrefManual                        ) return;
    if ( padInsideOther()                        ) return;
    if ( $padStartPage <> $padPage               ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )     ) return;
    if ( str_contains ( $padPage, 'develop/'   ) ) return;
    if ( str_contains ( $padPage, 'reference/' ) ) return;

    if ( $dir1 == 'tags'       and $dir2 <> 'pad'     ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXrefManualGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXrefPageSouce, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXrefPageSouce, $dir2 ) === FALSE ) return;
 
    padXrefManualGo ( $dir1, $dir2, $dir3 );

  }


  function padXrefManualGo ( $dir1, $dir2, $dir3 ) {

    padXrefGo ( padApp . '_xref', $dir1, $dir2, $dir3 );

  }


  function padXrefDevelop ( $dir1, $dir2, $dir3 ) {

    padXrefGo ( padData . 'xref', $dir1, $dir2, $dir3 );

  }


  function padXrefGo ( $dir, $dir1, $dir2, $dir3 ) {

    global $padPage;

    $dir .= "/$dir1/$dir2";

    if ( $dir3 )
      $dir .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $file = "$dir/" .  str_replace ( '/' , '@', padFileCorrect ($padPage ) ) . '.hit';

    padXrefFile ( $dir, $file );

  }


  function padXrefReverse ( $dir1, $dir2, $dir3 ) {

    global $pad, $padOccur, $padPage;

    $occur = $padOccur [$pad] ?? 0;

    padXrefOther ( "reverse/$padPage", 0, $dir1, $dir2, $dir3 );

  } 


  function padXrefXml ( $dir1, $dir2, $dir3 ) {

    global $pad, $padPage, $padXmlDir, $padXmlLevel, $padOccur, $padXmlDetails;

    $padXmlLvl = $padXmlLevel [$pad] ?? 0;
    $padXmlOcc = $padOccur    [$pad] ?? 0;

    $xref = padXrefXref ( $dir1, $dir2, $dir3 );

    if ( $padXmlDetails ) 
      padFileLog ( "$padXmlDir/details/$padXmlLvl/xref.txt", "$padXmlOcc $xref" );

    $padXmlLvl = $padXmlLevel [$pad] ?? 0;

    padXrefOther ( "$padXmlDir/xref", $padXmlLvl, $dir1, $dir2, $dir3, $padXmlLvl );   

  } 


  function padXrefTrace ( $dir1, $dir2, $dir3 ) {

    global $padTraceBase, $padTraceLine;

    padTrace ( 'xref', $dir1, "$dir2 $dir3" );

    padXrefOther ( "$padTraceBase/xref", $padTraceLine, $dir1, $dir2, $dir3, $padTraceLine );

  } 


  function padXrefOther ( $other, $number, $dir1, $dir2, $dir3, $xref='' ) {

    global $pad, $padOccur;

    $xref = trim ( $xref . ' ' . padXrefXref ( $dir1, $dir2, $dir3 ) );

    $occur = $padOccur [$pad] ?? 0;

    padFileLog ( "$other/xref.txt", "$pad/$occur " . $xref );

    $dir = padData . $other . "/$dir1/$dir2";

    if ( $dir3 )
      $dir .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $occur = $padOccur [$pad] ?? 0;

    $file = "$dir/$pad-$occur";
    if ( $number )
      $file .= "-$number";

    padXrefFile ( $dir, $file );

  } 


  function padXrefFile ( $dir, $file ) {

    if ( file_exists ( $file ) )
      return;

    if ( ! file_exists ( $dir ) )
      mkdir ( $dir, 0777, TRUE );

    touch ( $file, 0777, TRUE );

  } 


  function padXrefXref ( $dir1, $dir2, $dir3 ) {

    $xref = "$dir1-$dir2";

    if ( $dir3)
      $xref .= "-$dir3";

    return $xref;

  } 


?>