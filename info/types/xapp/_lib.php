<?php
  
  
  function padInfoXref ( $dir1, $dir2, $dir3='' ) {

    if ( ! $GLOBALS ['padInfoXref'] )
      return;

    global $padPage, $padInfoXrefSource, $padStartPage;

    if ( $dir1 == 'sequence' ) 
      return padInfoXrefGo ( '_xref', $dir1, $dir2, $dir3 );

    if ( padInsideOther ()                    ) return;
    if ( $padPage <> $padStartPage            ) return;
    if ( str_contains ( $padPage, 'develop' ) ) return;
    if ( str_contains ( $padPage, 'xref'    ) ) return;
    if ( str_contains ( $padPage, 'manual'  ) ) return;
    if ( ! isset ( $_REQUEST['padInclude']  ) ) return;
 
    if ( $dir1 == 'tag'        and $dir2 <> 'pad' ) return padInfoXrefGo ( '_xref', $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions'  and $dir2 <> 'pad' ) return padInfoXrefGo ( '_xref', $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padInfoXrefSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padInfoXrefSource, $dir2 ) === FALSE ) return;
 
    padInfoXrefGo ( '_xref', $dir1, $dir2, $dir3 );

  }
  

  function padInfoXrefGo ( $prefix, $dir1, $dir2, $dir3 ) {

    if ( $dir1 == 'properties' and ! file_exists ( "tag/$dir2.php" ) )
      $dir2 = strtolower($dir2);

    $file = "$prefix/$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = APP . "$file.txt";
    $page   = $GLOBALS ['padStartPage'];

    if ( file_exists ($target) and in_array ( $page, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padInfoLine ( "$file.txt", $page, 1 );

  }
 
 
?>