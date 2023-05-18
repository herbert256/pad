<?php

  $title = $reference;  
  $types = padData ('references.json');
 
  $type = $ref = $dir = $kind = '';

  foreach ( $types as $record ) {

    if ( $reference == $record['ref'] ) {
      extract ( $record );
      break;
    }

  }

?>