<?php

   foreach ( $padOptionsSingle as $padPrmName => $padPrmValue ) 
     if ( ! file_exists ( "/pad/sequence/options/types/$padPrmName.php" ) )
       if ( ! in_array ( $padPrmName , $padSeqDone ) )
         padError ( "Sequence: Unknow option: $padPrmName")

?>