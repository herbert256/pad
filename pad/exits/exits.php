<?php
  
  $padOutput = $padResult [0];
  $padOutput = padUnescape ( $padOutput );

  include pad . 'exits/myTidy.php';

  if ( $padTidy or strpos( $padOutput, '@tidy@' ) !== FALSE )
    include pad . 'exits/tidy.php';
 
  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include pad . 'cache/exits.php';

  include pad . 'exits/output.php';

?>