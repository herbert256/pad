<?php

  $title = $reference;  

  $type = $ref = $dir = $kind = '';

  $types = padData ('references.json');
 
  foreach ( $types as $record ) {
    if ( $reference == $record['ref'] ) {
      extract ( $record );
      break;
    }
  }

?>