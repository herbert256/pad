<?php

  function padStrClnFld ( $field ) {

    if ( str_starts_with ( $field, 'pad' ) ) 
      if ( ! str_starts_with ( $field, 'padStr' ) )
        if ( ! in_array ( $field, $GLOBALS ['padStrSto']) )
          if ( ! in_array ( $field, $GLOBALS ['padLevelVars']) )
            return TRUE;
         
    return FALSE;

  }

?>