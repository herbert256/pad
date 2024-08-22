<?php

  foreach ( $padPrm [$pad] as $padK => $padV )

    if ( file_exists ( "/pad/sequence/types/$pad/" ) 
      or file_exists ( "/pad/sequence/options/$padK.php" ) 
      or file_exists ( "/pad/sequence/actions/types/$padK.php") ) 

        padDone ( $padK );

?>