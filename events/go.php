<?php

  if ( $GLOBALS ['padInfoXref'] or $GLOBALS ['padInfoXref'] ) {

    if ( str_contains ($padTagContent.$padContent, 'content@') ) padInfoXapp ('constructs', 'content');  
    if ( str_contains ($padTagContent.$padContent, 'start@'  ) ) padInfoXapp ('constructs', 'start');  
    if ( str_contains ($padTagContent.$padContent, 'end@'    ) ) padInfoXapp ('constructs', 'end');  
    if ( str_contains ($padTagContent.$padContent, 'else@'   ) ) padInfoXapp ('constructs', 'else');  

  }

?>