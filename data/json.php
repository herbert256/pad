<?php

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