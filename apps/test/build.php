<?php

  // https://oeis.org/wiki/JSON_Format,_Compressed_Files

  $w = file ( '/home/herbert/host/Downloads/stripped', TRUE);

  foreach ( $w as $l ) 

    if ( str_starts_with ($l, 'A' ) ) {
    
      $i = (int) substr ( $l, 1,  7 );
    
      file_put_contents ( 
        PAD . "sequence/types/oeis/files/$i.php", 
        "<?php return [" . substr ( $l, 9, -2 ) . "] ?>" );
    
    }

?>