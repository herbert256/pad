<?php


  function padAtValue ( $field, $cor=0 ) {

    if ( str_contains($field, '@*') )
      return padAtValueAny ( $field, $cor);
        
    padAtCheckParts ( $field, $names, $parts );

    return padAt ( $names, $parts, $cor );

  }


  function padAtValueAny ( $field, $cor ) {

    global $pad;

    for ( $i=$pad; $i; $i-- ) {

      $check = str_replace ( '@*', "@$i", $field );

      if ( padAtCheck ( $check, $cor ) ) 
        return padAtValue ( $check, $cor );
    
    }

    return padAtValue ( str_replace ( '@*', "@any", $field ), $cor );

  }


?>