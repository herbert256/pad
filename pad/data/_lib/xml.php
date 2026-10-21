<?php

  // Turns XML text into a PAD data array; pulled in with include_once by data/xml.php.
  //
  //   padXmlToArray          strips a leading doctype/prolog, wraps the fragment in a
  //                          synthetic <x> root so several top-level elements are legal,
  //                          parses with SimpleXML and retries through tidy when that
  //                          fails, then unwraps the root back off the result
  //   padXmlToArrayIterator  walks the SimpleXML tree; a childless attribute-less element
  //                          becomes its text, otherwise attributes become fields (a name
  //                          clash gets an _ prefix) and children go under '_children'
  //   padXmlToArrayCheck     folds those '_children' entries up into their parent (a name
  //                          clash gets a _ suffix) and unwraps single-element lists, but for
  //                          an element that repeats in a sibling row
  //
  // Every element name is kept as a list so repeated tags iterate naturally in templates.

  // libxml's error mode is the request's, not this reader's: it is put back as it was on
  // every way out - it stayed switched to internal errors, which silenced the warnings of
  // the application's own XML code for the rest of the request.

  function padXmlToArray ( $data ) {

    $errors = libxml_use_internal_errors ( TRUE );

    try {
      return padXmlToArrayGo ( $data );
    } finally {
      libxml_use_internal_errors ( $errors );
    }

  }

  function padXmlToArrayGo ( $data ) {

    $input = str_replace ( '&nbsp;', ' ', trim($data) );

    // The prolog comes before the doctype in a document, so it goes first - the other way
    // round a standard xml declaration followed by a doctype left the doctype inside <x>.

    if ( str_starts_with($input, '<?') ) $input = trim ( substr ( $input, strpos($input, '>') + 1 ) );
    if ( str_starts_with($input, '<!') ) $input = trim ( substr ( $input, strpos($input, '>') + 1 ) );

    $input = "<x>$input</x>";

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

    // A document with no element - only a prolog or a comment - iterates to nothing, and
    // reset() of the empty array is FALSE, which the single-element unwrap and
    // padXmlToArrayCheck then walked with foreach: the request ended on "foreach() argument
    // must be of type array, false given" from inside the reader. No element is no rows.

    if ( ! $arr )
      return [];

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

        if ( $val !== '' )
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

  // A text element that repeats is a list in every sibling row, not only in the ones that have
  // more than one of it: a one-element list was unwrapped to its value wherever it stood, so
  // an order with two items held a list and an order with one a plain value, and
  // {item}{$item},{/item} iterated the letters' row of that value - "cc," for a single c.
  // $keep names the elements that repeat in a sibling of the row whose children these are;
  // a field that stands once in every row stays a value.

  function padXmlToArrayCheck ( $arr, $keep = [] ) {

    $multi = [];

    foreach ( $arr as $key => $val )
      if ( is_int ( $key ) and is_array ( $val ) and is_array ( $val ['_children'] ?? NULL ) )
        foreach ( $val ['_children'] as $name => $list )
          if ( is_array ( $list ) and count ( $list ) > 1 )
            $multi [$name] = TRUE;

    foreach ( $arr as $key => $val )
      if ( is_array ($val) )
        if ( count($val) == 1 and isset ($val[0]) and ! is_array ($val[0]) and ! isset ( $keep [$key] ) )
          $arr [$key] = $val [0];
        else
          $arr [$key] = padXmlToArrayCheck ( $arr [$key],
                                             is_int ( $key ) ? $multi : ( $key === '_children' ? $keep : [] ) );

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
