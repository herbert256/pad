<?php
  

  function padXweb ( $dir1, $dir2, $dir3='' ) {
 
    if ( $dir1 == 'tag'                                ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'constructs'                         ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'options'                            ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'properties'                         ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'                          ) padXwebManual ( $dir1, $dir2, $dir3 );
   
    if ( $dir1 == 'tag'        and $dir2 == 'tag'      ) padXwebManual ( 'properties', $dir3 );
    if ( $dir1 == 'field'      and $dir2 == 'tag'      ) padXwebManual ( 'properties', $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'property' ) padXwebManual ( 'properties', $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'     ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'types'    ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'   ) padXwebManual ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions'  ) padXwebManual ( $dir1, $dir2, $dir3 );
  
  }


  function padXwebManual ( $dir1, $dir2, $dir3='' ) {

    global $padPage, $padXwebSource, $padStartPage;

    if ( padInsideOther()                             ) return;
    if ( $padPage <> $padStartPage                    ) return;
    if ( ! str_ends_with ( padApp, '/pad/' )          ) return;
    if ( str_contains ( $padStartPage, 'develop'    ) ) return;
    if ( str_contains ( $padStartPage, 'weberence'  ) ) return;
    if ( str_contains ( $padStartPage, 'manual'     ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']          ) ) return;

    if ( $dir1 == 'tag'        and $dir2 <> 'pad'     ) return padXwebGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad'     ) return padXwebGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'actions' ) return padXwebGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'sequences'  and $dir2 == 'builds'  ) return padXwebGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'at'         and $dir2 == 'kind'    ) return padXwebGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padXwebSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padXwebSource, $dir2 ) === FALSE ) return;
 
    padXwebGo ( $dir1, $dir2, $dir3 );

  }


  function padXwebGo ( $dir1, $dir2, $dir3 ) {

    $file = "_xweb/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = padApp . "$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) and in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padInfoLine ( "$file.txt", $page, 1 );

  }
  
 
?>