<?php

  // field: - the ordinary variable lookup, searching the current level and its parents for a
  // field of that name and returning '' when there is none or it is not scalar.
  //
  // Under the strict check a field that is no value is named, as {field:x} as a tag and $x in
  // an expression name it (padEvalFieldReport) - unless ?? follows, which is there for a field
  // that may be missing. {echo field:nope} printed nothing where {field:nope} was an error.

  global $padCheckSyntax;

  if ( $padCheckSyntax ) {

    $padFieldNext = padEvalNextKey ( $result, $k );

    if ( ! ( $padFieldNext and $result [$padFieldNext] [1] == 'OPR' and $result [$padFieldNext] [0] === '??' ) )
      padEvalFieldReport ( $name, "field:$name" );

  }

  return padFieldValue ($name);

?>
