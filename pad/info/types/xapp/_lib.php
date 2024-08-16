<?php
  
  
  function $GLOBALS ['padInfo'] ( $dir1, $dir2, $dir3='' ) {

    padXappGo ( '_xref/noFilter', $dir1, $dir2, $dir3 );

    if ( $dir1 == 'tag'   and $dir2 == 'tag'      ) return $GLOBALS ['padInfo'] ( 'properties', $dir3 );
    if ( $dir1 == 'field' and $dir2 == 'tag'      ) return $GLOBALS ['padInfo'] ( 'properties', $dir3 );

    global $padPage, $padInfXappSource, $padStartPage;

    if ( padInsideOther ()                            ) return;
    if ( $padPage <> $padStartPage                    ) return;
    if ( ! str_ends_with ( '/app/', '/pad/' )          ) return;
    if ( str_contains ( $padStartPage, 'develop'    ) ) return;
    if ( str_contains ( $padStartPage, 'xref'       ) ) return;
    if ( str_contains ( $padStartPage, 'manual'     ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']          ) ) return;

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXappGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'  ) return padXappGo ( '_xref', $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padInfXappSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padInfXappSource, $dir2 ) === FALSE ) return;
 
    padXappGo ( '_xref', $dir1, $dir2, $dir3 );

  }
  

  function padXappGo ( $prefix, $dir1, $dir2, $dir3 ) {

    $file = "$prefix/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = "/app/$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) )
      if ( in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
        return;

    padInfoLine ( "$file.txt", $page, 1 );

  }
 
?>