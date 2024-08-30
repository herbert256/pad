<?php

  foreach ( $padPrm [$pad] as $padK => $padV )

    if ( file_exists ( "/pad/sequence/types/$pad/" ) 
      or file_exists ( "/pad/sequence/options/types/$padK.php" ) 
      or file_exists ( "/pad/sequence/actions/types/$padK.php")
      or file_exists ( "/pad/sequence/stores/types/$padK.php") ) 

        padDone ( $padK );

?>