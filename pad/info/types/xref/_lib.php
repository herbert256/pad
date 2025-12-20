<?php

  function padInfoXref ( $dir1, $dir2, $dir3='' ) {

    global $padInfoXref, $padInfoXrefSource, $padPage, $padStartPage;

    if ( ! $padInfoXref )
      return;

    if ( $dir1 == 'sequence' )
      return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if ( padInsideOther ()         ) return;
    if ( $padPage <> $padStartPage ) return;

    if ( $dir1 == 'tag'       and $dir2 <> 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );
    if ( $dir1 == 'functions' and $dir2 <> 'pad' ) return padInfoXrefGo ( $dir1, $dir2, $dir3 );

    if (   $dir3 and strpos ( $padInfoXrefSource, $dir3 ) === FALSE ) return;
    if ( ! $dir3 and strpos ( $padInfoXrefSource, $dir2 ) === FALSE ) return;

    padInfoXrefGo ( $dir1, $dir2, $dir3 );

  }

  function padInfoXrefGo ( $dir1, $dir2, $dir3 ) {

    global $padStartPage;

    if ( $dir1 == 'properties' and ! file_exists ( PAD . "properties/$dir2.php" ) )
      $dir2 = strtolower($dir2);

    $file = "$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    if ( $dir1 <> 'sequence' )
      $file = "xref/$file";

    $target = APP . "reference/DATA/$file.txt";

    if ( file_exists ($target) and in_array ( $padStartPage, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    filePutLine ( 'reference', "$file.txt", $padStartPage );

  }

?>