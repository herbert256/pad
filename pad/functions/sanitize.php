<?php

  // Pipe function sanitize: encodes <, >, & and both quote styles - and every other
  // character that has a named entity - without encoding an entity twice. It is the step
  // the default data chain runs on every {$field}.
  //
  // This is FILTER_SANITIZE_FULL_SPECIAL_CHARS spelled out as htmlentities, which gives the
  // same answer for every valid string. The difference is invalid UTF-8: the filter answered
  // the empty string for the whole value, so one broken byte blanked the field; ENT_SUBSTITUTE
  // replaces just that byte with U+FFFD. Arrays and objects keep the filter's answer.

  if ( is_array ( $value ) or is_object ( $value ) )
    return filter_var ( $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

  return htmlentities ( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', FALSE );

?>