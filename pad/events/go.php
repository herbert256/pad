<?php

  // Fires from level/go.php right after a tag's type handler has run, and notes in the xref
  // report which of the content@ / start@ / end@ / else@ constructs the tag's own output or
  // its content contains.

  global $padInfoXref;

  if ( $padInfoXref  ) {

    if ( str_contains ($padTagContent.$padContent, 'content@') ) padInfoXref ('constructs', 'content');
    if ( str_contains ($padTagContent.$padContent, 'start@'  ) ) padInfoXref ('constructs', 'start');
    if ( str_contains ($padTagContent.$padContent, 'end@'    ) ) padInfoXref ('constructs', 'end');
    if ( str_contains ($padTagContent.$padContent, 'else@'   ) ) padInfoXref ('constructs', 'else');

  }

?>