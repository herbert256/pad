<?php

  // Reads $data as CSV and returns it as a PAD data array, one row per line keyed by the
  // names in the header line. Included by padData() as data/<type>.php once padContentType
  // has settled on 'csv' - which it does last of all, so this is also the catch-all reader
  // for anything not recognised as list, json, yaml, xml, html, range, curl or file.
  //
  // Quoted fields are urlencoded (and doubled "" parked as !!Q!!) before the split, so
  // commas and newlines inside quotes survive the naive explode, then decoded per cell.

  $result = [];

  $enc = preg_replace_callback(
      '/"(.*?)"/s',
      function ($field) {
          return urlencode(mb_convert_encoding($field[1], 'UTF-8', 'ISO-8859-1'));
      },
      preg_replace('/(?<!")""/', '!!Q!!', trim($data))
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

?>