<?php

  // Fixture: an application tag that decodes its parameter as JSON - a literal the template
  // wrote, which reaches the tag as written.

  $jsonParm = json_decode ( $padParm, TRUE );

  return is_array ( $jsonParm ) ? 'ok:' . ( $jsonParm ['a'] ?? '' ) : 'bad';

?>
