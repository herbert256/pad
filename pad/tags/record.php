<?php

  // The {pad:record} tag and, through one-line includes, {pad:field} and {pad:array}: runs the tag's
  // parameter as SQL, using the tag name itself as db()'s command word - so {pad:array "* from
  // users"} becomes db("array * from users"). $padTag [$pad] is read at run time, which is
  // what lets the three tags share this one file; {pad:check} has its own boolean variant. The
  // SQL is a quoted string with its values put in as $name (padDbTagSql in lib/db.php).

  // In the designer preview a tag with a name the sample holds answers from the sample
  // instead of the database - {pad:array "* from orders", name='orders'} - and a capture of
  // sample data records the database's answer under that name (lib/sample.php).

  if ( padSampleFound ( padTagParm ( 'name' ), $padSampleValue ) )
    return $padSampleValue;

  $padRecordSql = padDbTagSql ();

  if ( $padRecordSql === FALSE )
    return ( $padTag [$pad] == 'field' ) ? '' : [];

  $padRecordResult = db ( $padTag [$pad] . ' ' . $padRecordSql );

  if ( $padSampleMode == 'capture' and is_array ( $padSampleData ) and is_string ( padTagParm ( 'name' ) ) and padTagParm ( 'name' ) !== '' )
    $padSampleData [ padTagParm ( 'name' ) ] = $padRecordResult;

  return $padRecordResult;

?>
