<?php

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if ( $padPrmKind == 'option' ) 
      if ( file_exists ( "sequence/options/types/$padPrmName.php") )
        $padSeqInfo ['options'] [] = $padPrmName;
    
  }
 
?>