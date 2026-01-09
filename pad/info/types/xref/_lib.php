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

    global $padApp, $padStartPage;

    if ( $dir1 == 'properties' and ! file_exists ( PAD . "properties/$dir2.php" ) )
      $dir2 = strtolower($dir2);

    $file = "$dir1/$dir2";

    if ( $dir3 !== '' )
      $file .= "/$dir3";

    $target = "reference/$file.txt";
    $xref   = "$padApp;$padStartPage";

    if ( file_exists ($target) and in_array ( $xref, file ( $target, FILE_IGNORE_NEW_LINES ) ) )
      return;

    padFilePut ( $target, $xref, 1);

  }

?>