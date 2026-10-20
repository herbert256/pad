<?php

  // field: - the ordinary variable lookup, searching the current level and its parents for a
  // field of that name and returning '' when there is none or it is not scalar.
  //
  // Under the strict check a field that is no value is named, as {field:x} as a tag and $x in
  // an expression name it (padEvalFieldReport) - unless its value goes into a fallback, ?? or
  // default, which is there for a field that may be missing ($padEvalCoalesce, padEvalLook).
  // {echo field:nope} printed nothing where {field:nope} was an error.

  global $padCheckSyntax;

  $padFieldValue = padFieldValue ($name);

  if ( $padCheckSyntax and $padFieldValue === '' and ! $padEvalCoalesce )
    padEvalFieldReport ( $name, "field:$name" );

  return $padFieldValue;

?>
