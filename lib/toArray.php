<?php


  function padCsvToArray ( $csv ) {

    $result = [];

    $enc = preg_replace_callback(
        '/"(.*?)"/s',
        function ($field) {
            return urlencode(utf8_encode($field[1]));
        },
        preg_replace('/(?<!")""/', '!!Q!!', trim($csv))
    );
    
    $lines  = preg_split('/( *\R)+/s', $enc);
    $header = explode(',', $lines [0]);
 
    foreach ($header as $key1 => $val1)
      $header [$key1] = trim(str_replace('!!Q!!', '"', urldecode($val1)));
 
    foreach ($lines as $key1 => $val1)
      if ($key1 > 0)
        foreach (explode(',', $val1) as $key2 => $val2)
          $result [$key1] [$header[$key2]] = trim(str_replace('!!Q!!', '"', urldecode($val2)));

    if ( ! is_array($result) or $result === NULL or $result === FALSE )
      return padError ( "CSV conversion error" );

    return $result;

  }


  function padHtmlToArray ( $html ) {

    $tidyoptions = [
      'output-xml'   => true,
      'force-output' => true
    ];

    $xml = new tidy;
    $xml->parseString($data, $tidyoptions, 'utf8');
    $xml->cleanRepair();

    if ( $xml === FALSE )
      return padError ( "TIDY conversion error");

    return padXmlToArray ( $xml );

  }


  function padYamlToArray ( $yaml ) {

    $result = yaml_parse ($yaml);

    if ( ! is_array($result) or $result === NULL or $result === FALSE)
      return padError ( "YAML parse error" );

    return $result;

  }


  function padJsonToArray ( $json ) {

    $json = str_replace(['&open;', '&close;'], ['{', '}'], $json );

    $first1 = strpos  ($json, '{');
    $last1  = strrpos ($json, '}');

    $first2 = strpos  ($json, '[');
    $last2  = strrpos ($json, ']');

    if ($first1 !== FALSE and $last1 !== FALSE and ($first2 === FALSE or $first1 < $first2) )
      $json = substr($json, $first1, ($last1-$first1)+1);
    elseif ($first2 !== FALSE and $last2 !== FALSE and ($first1 === FALSE or $first2 < $first1) )
      $json = substr($json, $first2, ($last2-$first2)+1);
    else
      return padError ( "JSON conversion error");

    $result = json_decode($json, true);
    
    if ( ! is_array($result) or $result === NULL or $result === FALSE)
      return padError ( "JSON error (decode): " . json_last_error() . ' - ' . json_last_error_msg() );

    return $result;

  }


  function padXmlToArray ( $xml ) {

    $xml = str_replace('&nbsp;', ' ', $xml);

    if ( str_starts_with($xml, '<?xml') ) {
      $xml   = str_replace("\r\n", "\n", $xml);
      $lines = explode("\n", $xml);
      unset ( $lines [0] );
      $xml = implode("\n", $lines);
    }

    $xml = "<padXmlToArray>$xml</padXmlToArray>";

    libxml_use_internal_errors ( true );

    $xml = simplexml_load_string ( $xml, "SimpleXMLElement", LIBXML_NOERROR | LIBXML_NOWARNING );

    if ( $xml  === FALSE ) {
        $errors = libxml_get_errors ();
        $line = 'XML parse error: ';
        foreach ( $errors as $error )
          $line .= " Line/column: $error->line/$error->column: " . trim($error->message);
        libxml_clear_errors ();
        return padError ($line);
    }

    $arr = padXmlToArrayIterator ( $xml );

    $arr = reset($arr);

    if ( count ($arr) == 1 and isset ($arr[0]) and is_array ($arr[0]) )
      $arr = $arr [0];

    return padXmlToArrayCheck ( $arr );

  }
  

  function padXmlToArrayIterator ( $xml ) {

    $arr = array();

    for( $xml->rewind(); $xml->valid(); $xml->next() ) {

      $val = trim ( strval ( $xml->current() ) );
      $idx = $xml->key();
      $cnt = ( array_key_exists ($idx, $arr) ) ? array_key_last ($arr [$idx]) + 1 : 0;

      if ( ! $xml->hasChildren() and ! count ($xml->current()->attributes()) ) 

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