<?php

  $w = file ( '/home/herbert/host/Downloads/stripped', TRUE);

  foreach ( $w as $l ) {

    $i = (int) substr ( $l, 1,  7 );

    $a [$i] = substr ( $l, 9, -2 ); 

  }

  $t = "<?php\n\nconst OEIS = [";

  for ($i=0; $i <=375610 ; $i++) { 

    if ( isset ( $a [$i] ) )
      $s = $a [$i];
    else
      $s = '';

    $t .= "\n[$s],";

  }

  $t = substr ( $t, 0, -1) . "\n];\n\n?>";

  file_put_contents ( "/pad/sequence/types/oeis/test.php", $t );

?>