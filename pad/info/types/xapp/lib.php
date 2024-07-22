<?php
  
  
  function padXapp ( $dir1, $dir2, $dir3='' ) {

    if ( $dir1 == 'tag'   and $dir2 == 'tag'      ) return padXapp ( 'properties', $dir3 );
    if ( $dir1 == 'field' and $dir2 == 'tag'      ) return padXapp ( 'properties', $dir3 );

    global $padPage, $padXappSource, $padStartPage;

    if ( padInsideOther ()                            ) return;
    if ( $padPage <> $padStartPage                    ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )          ) return;
    if ( str_contains ( $padStartPage, 'develop'    ) ) return;
    if ( str_contains ( $padStartPage, 'xref'       ) ) return;
    if ( str_contains ( $padStartPage, 'manual'     ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']          ) ) return;

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padXappGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXappGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXappGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'  ) return padXappGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXappSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXappSource, $dir2 ) === FALSE ) return;
 
    padXappGo ( $dir1, $dir2, $dir3 );

  }


  function padXappGo ( $dir1, $dir2, $dir3 ) {

    $file = "_xref/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = padApp . "$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) )
      if ( in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
        return;

    padInfoLine ( "$file.txt", $page, 1 );

  }
  
 
?>