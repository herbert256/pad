<?php
  
  
  function padInfoXapp ( $dir1, $dir2, $dir3='' ) {

    if ( $GLOBALS ['padInfoXref'] )
      padInfoXref ( $dir1, $dir2, $dir3 );

    if ( ! $GLOBALS ['padInfoXapp'] )
      return;

    if ( $dir1 == 'tag'   and $dir2 == 'tag' ) padInfoXapp ( 'properties', $dir3 );
    if ( $dir1 == 'field' and $dir2 == 'tag' ) padInfoXapp ( 'properties', $dir3 );

    global $padPage, $padInfoXappSource, $padStartPage;

    if ( padInsideOther ()                    ) return padInfoXappGo ( '_xref/skip/1', $dir1, $dir2, $dir3 );
    if ( $padPage <> $padStartPage            ) return padInfoXappGo ( '_xref/skip/2', $dir1, $dir2, $dir3 );
    if ( str_contains ( $padPage, 'develop' ) ) return padInfoXappGo ( '_xref/skip/3', $dir1, $dir2, $dir3 );
    if ( str_contains ( $padPage, 'xref'    ) ) return padInfoXappGo ( '_xref/skip/4', $dir1, $dir2, $dir3 );
    if ( str_contains ( $padPage, 'manual'  ) ) return padInfoXappGo ( '_xref/skip/5', $dir1, $dir2, $dir3 );
    if ( ! isset ( $_REQUEST['padInclude']  ) ) return padInfoXappGo ( '_xref/skip/6', $dir1, $dir2, $dir3 );

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padInfoXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padInfoXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padInfoXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'  ) return padInfoXappGo ( '_xref', $dir1, $dir2, $dir3 );

    if ( $dir3 and strpos ( $padInfoXappSource, $dir3 ) === FALSE ) 
      return padInfoXappGo ( '_xref/skip/7', $dir1, $dir2, $dir3 );

    if ( ! $dir3 and strpos ( $padInfoXappSource, $dir2 ) === FALSE ) 
      return padInfoXappGo ( '_xref/skip/8', $dir1, $dir2, $dir3 );
 
    padInfoXappGo ( '_xref', $dir1, $dir2, $dir3 );

  }
  

  function padInfoXappGo ( $prefix, $dir1, $dir2, $dir3 ) {

    $file = "$prefix/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = "/app/$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) and in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padInfoLine ( "$file.txt", $page, 1 );

  }
 
 
?>