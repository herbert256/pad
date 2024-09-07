<?php

  foreach ( $padPrm [$pad] as $padK => $padV )

    if ( file_exists ( "/pad/sequence/types/$padK" ) 
      or file_exists ( "/pad/sequence/options/types/$padK.php" ) 
      or file_exists ( "/pad/sequence/after/actions/types/$padK.php") ) 

        padDone ( $padK );

?>