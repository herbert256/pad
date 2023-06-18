<?php

  $xml = str_replace ( '&nbsp;', ' ', $data );

  if ( str_starts_with($xml, '<?xml') )
    $xml = substr ( $xml, strpos($xml, '>') + 1 );

  $xml = "<x>$xml</x>";

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

?>