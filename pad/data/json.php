<?php

  // Reads $data as JSON and returns it as a PAD data array. Included by padData() as
  // data/<type>.php.
  //
  // Braces first have to be put back: template text reaches here with { and } already
  // protected as &open; / &close; so the tag scanner left the JSON alone. Whatever sits
  // outside the outermost {...} or [...] pair is then trimmed off, which lets a JSON blob
  // embedded in surrounding text be used directly.

  $data = str_replace ( ['&open;', '&close;'], ['{', '}'], $data );

  $first1 = strpos  ($data, '{');
  $last1  = strrpos ($data, '}');

  $first2 = strpos  ($data, '[');
  $last2  = strrpos ($data, ']');

  if ($first1 !== FALSE and $last1 !== FALSE and ($first2 === FALSE or $first1 < $first2) )
    $data = substr($data, $first1, ($last1-$first1)+1);
  elseif ($first2 !== FALSE and $last2 !== FALSE and ($first1 === FALSE or $first2 < $first1) )
    $data = substr($data, $first2, ($last2-$first2)+1);
  else
    return padError ( "JSON conversion error");

  $result = json_decode($data, true);

  if ( ! is_array($result) or $result === NULL or $result === FALSE )
    return padError ( "JSON error (decode): " . json_last_error() . ' - ' . json_last_error_msg() );

  return $result;

?>