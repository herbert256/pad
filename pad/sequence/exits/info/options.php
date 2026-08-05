<?php

  // Info block: lists which sequence options the tag actually carried, by walking the parsed
  // parameters $padParms[$pad] and keeping the 'option' ones that name a file in
  // options/types/. Appends to $pqInfo['options'].

  foreach ( $padParms [$pad] as $padStartOption ) {

    extract ( $padStartOption );

    if ( $padPrmKind == 'option' )
      if ( file_exists ( PQ . "options/types/$padPrmName.php") )
        $pqInfo ['options'] [] = $padPrmName;

  }

?>