<?php

  function padXmlToArray ( $data ) {

    $input = str_replace ( '&nbsp;', ' ', trim($data) );

    if ( str_starts_with($input, '<!') ) $input = substr ( $input, strpos($input, '>') + 1 );
    if ( str_starts_with($input, '<?') ) $input = substr ( $input, strpos($input, '>') + 1 );

    $input = "<x>$input</x>";

    libxml_use_internal_errors ( true );
    $xml = simplexml_load_string ( $input, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_NOWARNING );

    if ( $xml === FALSE ) {

      libxml_clear_errors ();

      $options = [
        'input-xml'    => true,
        'output-xml'   => true,
        'force-output' => true
      ];
      
      $xml = new tidy;
      $xml->parseString ( $input, $options, 'utf8');
      $xml->cleanRepair();
      
      if ( $xml === FALSE )
        return padError ( "TIDY conversion error" );
    
      $xml = simplexml_load_string ($xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_ERR_NONE);
      
      if ( $xml === FALSE ) {
        $errors = libxml_get_errors ();
        $line = 'XML parse error: ';
        foreach ( $errors as $error )
          $line .= " Line/column: $error->line/$error->column: " . trim($error->message);
        libxml_clear_errors ();
        return padError ($line);
      }

    }

    $arr = padXmlToArrayIterator ( $xml );
    $arr = reset($arr);

    if ( is_array($arr) and count ($arr) == 1 and isset ($arr[0]) and is_array ($arr[0]) )
      $arr = $arr [0];

    $arr = padXmlToArrayCheck ( $arr );

    return $arr;

  }


  function padXmlToArrayIterator ( $xml ) {

    $arr = array();
 
    for( $xml->rewind(); $xml->valid(); $xml->next() ) {

      $val = trim ( strval ( $xml->current() ) );
      $idx = $xml->key();
      $cnt = ( array_key_exists ($idx, $arr) ) ? array_key_last ($arr [$idx]) + 1 : 0;

      if ( ! $xml->hasChildren() and ! count ( $xml->current()->attributes() ) ) 

        $arr [$idx] [$cnt] = $val;

      else {

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