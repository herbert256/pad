<?php


  function padXrefManual ( $dir1, $dir2, $dir3 ) {

    global $padPage, $padXrefPageSouce;

    if ( str_starts_with ( $padPage, 'develop/'   ) ) return;
    if ( str_starts_with ( $padPage, 'reference/' ) ) return;

    if ( $dir1 == 'tags'       and $dir2 <> 'pad'     ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXrefManualGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXrefManualGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXrefPageSouce, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXrefPageSouce, $dir2 ) === FALSE ) return;
 
    padXrefManualGo ( $dir1, $dir2, $dir3 );

  }


  function padXrefManualGo ( $dir1, $dir2, $dir3 ) {

    padXrefGo ( padApp . "xref/$dir1/$dir2", $dir3, FALSE );

  }


  function padXref ( $dir1, $dir2, $dir3='', $reverse=FALSE ) {

    global $padPage;

    if ( $GLOBALS ['padXrefReverse'] and ! $reverse )
      padXref ( $dir1, $dir2, $dir3, TRUE );

    if ( $dir1 == 'tags'   and $dir2 == 'tag'         ) padXref ( 'properties', $dir3, '', $reverse );
    if ( $dir1 == 'fields' and $dir2 == 'tag'         ) padXref ( 'properties', $dir3, '', $reverse );
    if ( $dir1 == 'at'     and $dir2 == 'property'    ) padXref ( 'properties', $dir3, '', $reverse );

    if ( $dir1 == 'tags'                              ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'parms'      and $dir2 == 'options' ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                        ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                         ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'   ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) padXrefManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) padXrefManual ( $dir1, $dir2, $dir3 );


    if ( ! $reverse )
      padXrefGo ( padData . "xref/$dir1/$dir2", $dir3, FALSE );
    else
      padXrefGo ( padData . "reverse/$padPage/$dir1/$dir2", $dir3, FALSE );

  }


  function padXrefGo ( $dir, $dir3, $reverse ) {

    global $padPage;

    if ( $dir3 )
      $dir .= "/" . str_replace ( '/' , '@', padFileCorrect ($dir3 ) );

    $file = "$dir/" .  str_replace ( '/' , '@', padFileCorrect ($padPage ) ) . '.hit';

    if ( file_exists ( $file ) )
      return;

    if ( ! file_exists ( $dir ) )
      mkdir ( $dir, 0777, TRUE );

    if ( ! $reverse )
      touch ( $file, 0777, TRUE );

  }

  
?>