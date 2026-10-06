<?php

  // The {record} tag and, through one-line includes, {field} and {array}: runs the tag's
  // parameter as SQL, using the tag name itself as db()'s command word - so {array "* from
  // users"} becomes db("array * from users"). $padTag [$pad] is read at run time, which is
  // what lets the three tags share this one file; {check} has its own boolean variant.

  // In the designer preview a tag with a name the sample holds answers from the sample
  // instead of the database - {array "* from orders", name='orders'} - and a capture of
  // sample data records the database's answer under that name (lib/sample.php).

  if ( padSampleFound ( padTagParm ( 'name' ), $padSampleValue ) )
    return $padSampleValue;

  $padRecordResult = db ( $padTag [$pad] . ' ' . $padParm );

  if ( $padSampleMode == 'capture' and is_array ( $padSampleData ) and is_string ( padTagParm ( 'name' ) ) and padTagParm ( 'name' ) !== '' )
    $padSampleData [ padTagParm ( 'name' ) ] = $padRecordResult;

  return $padRecordResult;

?>
