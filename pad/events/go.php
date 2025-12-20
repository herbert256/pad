<?php

  global $padInfoXref;

  if ( $padInfoXref  ) {

    if ( str_contains ($padTagContent.$padContent, 'content@') ) padInfoXref ('constructs', 'content');
    if ( str_contains ($padTagContent.$padContent, 'start@'  ) ) padInfoXref ('constructs', 'start');
    if ( str_contains ($padTagContent.$padContent, 'end@'    ) ) padInfoXref ('constructs', 'end');
    if ( str_contains ($padTagContent.$padContent, 'else@'   ) ) padInfoXref ('constructs', 'else');

  }

?>
