<?php

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if ( $padPrmKind == 'option' )
      if ( file_exists ( PQ . "options/types/$padPrmName.php") )
        $pqInfo ['options'] [] = $padPrmName;

  }

?>