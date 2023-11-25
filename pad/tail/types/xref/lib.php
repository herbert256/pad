<?php
  

  function padXref ( $dir1, $dir2, $dir3='' ) {

    global $padXrefTail, $padXrefDevelop, $padXrefXml, $padXrefTrace, $padXrefId, $padXrefManual;

    $padXrefId++;

    padTail ( 'xref', $dir1, $dir2, $dir3 );

    if ( $padXrefTail    ) padXrefTail    ( $dir1, $dir2, $dir3 );
    if ( $padXrefManual  ) padXrefManual  ( $dir1, $dir2, $dir3 );
    if ( $padXrefDevelop ) padXrefDevelop ( $dir1, $dir2, $dir3 );
    if ( $padXrefXml     ) padXrefXml     ( $dir1, $dir2, $dir3 );
    if ( $padXrefTrace   ) padXrefTrace   ( $dir1, $dir2, $dir3 );
 
  }

  
  function padXrefManual ( $dir1, $dir2, $dir3 ) {
   
    if ( $dir1 == 'tags'       and $dir2 == 'tag'      ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'fields'     and $dir2 == 'tag'      ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'property' ) padXrefManual2 ( 'properties', $dir3 );
    if ( $dir1 == 'tags'                               ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'constructs'                         ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'parms'      and $dir2 == 'options'  ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                         ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                          ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'    ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions'  ) padXrefManual2 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'     ) padXrefManual2 ( $dir1, $dir2, $dir3 );
  
  }


  function padXrefManual2 ( $dir1, $dir2, $dir3='' ) {

    global $padPage, $padXrefPageSource, $padXrefManual, $padStartPage;

    if ( padInsideOther()                        ) return;
    if ( $padStartPage <> $padPage               ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )     ) return;
    if ( str_contains ( $padPage, 'develop/'   ) ) return;
    if ( str_contains ( $padPage, 'reference/' ) ) return;

    if ( $dir1 == 'tags'       and $dir2 <> 'pad'     ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXrefManual3 ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXrefManual3 ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXrefPageSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXrefPageSource, $dir2 ) === FALSE ) return;
 
    padXrefManual3 ( $dir1, $dir2, $dir3 );

  }


  function padXrefManual3 ( $dir1, $dir2, $dir3 ) {

    padXrefManualDevelop ( padApp . '_xref', $dir1, $dir2, $dir3 );

  }


  function padXrefDevelop ( $dir1, $dir2, $dir3 ) {

    padXrefManualDevelop ( padData . 'xref', $dir1, $dir2, $dir3 );

  }


  function padXrefManualDevelop ( $dir, $dir1, $dir2, $dir3 ) {

    global $padPage;

    $dir .= "/$dir1/$dir2";

    if ( $dir3 )
      $dir .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $file = "$dir/" .  str_replace ( '/' , '@', padFileCorrect ($padPage ) ) . '.hit';

    if ( file_exists ( $file ) )
      return;

    if ( ! file_exists ( $dir ) )
      mkdir ( $dir, 0777, TRUE );

    touch ( $file, 0777, TRUE );

  }


  function padXrefTail ( $dir1, $dir2, $dir3 ) {

    global $padTailDir;
    
    padXrefLine ( 'xref', $dir1, $dir2, $dir3 );

  }
  
  
  function padXrefXml ( $dir1, $dir2, $dir3 ) {

    global $pad, $padXml, $padXmlLevel, $padXmlDetails;
  
    if ( ! $padXml        ) return;
    if ( ! $padXmlDetails ) return;
 
    $target = $padXmlLevel [$pad] ?? 0;
  
    padXrefLine ( "xml/details/$target", $dir1, $dir2, $dir3 );
  
  } 


  function padXrefTrace ( $dir1, $dir2, $dir3 ) {

    global $pad, $padTrace, $padTraceLevel;
  
    if ( ! $padTrace ) 
      return;
    
    $target = $padTraceLevel [$pad] ?? 0;
  
    padXrefLine ( "trace/$target/xref", $dir1, $dir2, $dir3 );
 
  } 


  function padXrefLine ( $target, $dir1, $dir2, $dir3 ) {

    global $padTailDir;
  
    if ( $dir3 )
      $xref = "$dir2/" . padFileCorrect ($dir3 );
    else
      $xref = padFileCorrect ($dir2 );
  
    padTailLine ( "$padTailDir/$target/$dir1/$xref.txt", padTailIds () );

  }

    
?>