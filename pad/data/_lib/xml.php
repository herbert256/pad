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
  //   padXmlToArraySpaced    the same walk for a document with prefixed names, which takes
  //                          the elements and attributes of every namespace
  //   padXmlToArrayCheck     folds those '_children' entries up into their parent (a name
  //                          clash gets a _ suffix) and unwraps single-element lists
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

    // SimpleXML's own iteration walks the children and attributes of an element's own
    // namespace only: an element or attribute with a prefix - an RSS item's dc:creator and
    // content:encoded, xml:lang, xlink:href - was dropped without a word. A document that
    // has such names is read by padXmlToArraySpaced, which takes them all.

    if ( array_filter ( array_keys ( $xml->getDocNamespaces ( TRUE ) ) ) or preg_match ( '/\sxml:[\w-]+\s*=/', $input ) )
      $arr = padXmlToArraySpaced ( $xml, [ padXmlSpacedClash ( $xml->xpath ( '//*' ) ?: [] ),
                                           padXmlSpacedClash ( $xml->xpath ( '//@*' ) ?: [] ) ] );
    else
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

  // padXmlToArrayIterator for a document with prefixed names: every child element and every
  // attribute, in document order, whatever its namespace. A prefixed name is kept under its
  // local name - {$creator}, as a field name holds no colon - and under prefix_name where
  // another namespace uses the same local name in the document: <title> and <dc:title> are
  // title and dc_title. $clash holds those local names, of the elements and of the attributes
  // (padXmlSpacedClash), so one kind of element has one name in every row.

  function padXmlToArraySpaced ( $xml, $clash ) {

    $arr = [];

    foreach ( padXmlSpacedNames ( $xml->xpath ( '*' ) ?: [], $clash [0] ) as [ $idx, $el ] ) {

      $val   = trim ( strval ( $el ) );
      $cnt   = ( array_key_exists ($idx, $arr) ) ? array_key_last ($arr [$idx]) + 1 : 0;
      $kids  = $el->xpath ( '*' ) ?: [];
      $attrs = padXmlSpacedNames ( $el->xpath ( '@*' ) ?: [], $clash [1] );

      if ( ! $kids and ! $attrs )

        $arr [$idx] [$cnt] = $val;

      else {

        if ( $val !== '' )
          $arr [$idx] [$cnt] [$idx] = $val;

        foreach ( $attrs as [ $key, $att ] )
          if ( isset ( $arr [$idx] [$cnt] [$key] ) )
            $arr [$idx] [$cnt] ['_'.$key] = strval($att);
          else
            $arr [$idx] [$cnt] [$key] = strval($att);

        if ( $kids )
          $arr [$idx] [$cnt] ['_children'] = padXmlToArraySpaced ( $el, $clash );

      }

    }

    return $arr;

  }

  // The local names that stand in more than one namespace among $nodes - all the elements,
  // or all the attributes, of the document. Counted per element, an item with <author> and
  // <itunes:author> had author and itunes_author, an item with <itunes:author> alone author:
  // {$itunes_author} was not found on the second.

  function padXmlSpacedClash ( $nodes ) {

    $spaces = [];

    foreach ( $nodes as $node )
      $spaces [ $node->getName () ] [ (string) array_key_first ( $node->getNamespaces () ) ] = TRUE;

    return array_filter ( $spaces, fn ( $set ) => count ( $set ) > 1 );

  }

  // The names the nodes of one element are kept under, as [ name, node ] pairs in their order:
  // the local name, or prefix_name when that local name stands in more than one namespace of
  // the document ($clash).

  function padXmlSpacedNames ( $nodes, $clash ) {

    $named = [];

    foreach ( $nodes as $node ) {

      $local  = $node->getName ();
      $prefix = (string) array_key_first ( $node->getNamespaces () );

      $named [] = [ ( $prefix !== '' and isset ( $clash [$local] ) ) ? $prefix . '_' . $local : $local, $node ];

    }

    return $named;

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
