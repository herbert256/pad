<?php


  function padXmlToArray ( $xml ) {

    $xml = new SimpleXmlIterator ( $xml, LIBXML_NOERROR | LIBXML_ERR_NONE );

    $arr = padXmlToArrayIterator ( $xml );

    return padXmlToArrayCheck ( $arr );

  }
  

  function padXmlToArrayIterator ( $xml ) {

    $arr = array();

    for( $xml->rewind(); $xml->valid(); $xml->next() ) {

      $val = trim ( strval ( $xml->current() ) );
      $idx = $xml->key();
      $cnt = ( array_key_exists ($idx, $arr) ) ? array_key_last ($arr [$idx]) + 1 : 0;

      if ( ! $xml->hasChildren() and ! count ( $xml->current()->attributes() ) ) {
        $arr [$idx] [$cnt] = $val;
        continue;
      }

      if ( $val )
        $arr [$idx] [$cnt] [$idx] = $val;

      foreach ( $xml->current()-> attributes() as $key => $val)
        if ( isset ( $arr [$idx] [$cnt] [$key] ) )
          $arr [$idx] [$cnt] ['_'.$key] = strval($val);
        else
          $arr [$idx] [$cnt] [$key] = strval($val);
      
      if ( $xml->hasChildren() )
        $arr [$idx] [$cnt] ['_children'] = padXmlToArrayIterator ($xml->current());

    }

    return $arr;

  }


  function padXmlToArrayCheck ( $arr ) {

    foreach ( $arr as $key => $val ) 
      if ( is_array ($val) )
        if ( count($val) == 1 and isset ($val[0]) and ! is_array ($val[0]) )
          $arr [$key] = $val [0];
        else
          $arr [$key] = padXmlToArrayCheck ( $arr [$key] ); 

    foreach ( $arr as $key => $val ) 
      if ( $key == '_children') {
        unset ( $arr [$key] );
        foreach ( $val as $key2 => $val2)
          if ( isset ( $arr [$key2] ) )
            $arr [$key2.'_'] = $val2;
          else
            $arr [$key2] = $val2;
      }

    return $arr;
  
  }


?>