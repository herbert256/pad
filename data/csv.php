<?php

  $result = [];

  $enc = preg_replace_callback(
      '/"(.*?)"/s',
      function ($field) {
          return urlencode(utf8_encode($field[1]));
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