<?php

  // padAttempt with no user checks the password against a hash of nobody's, so that an
  // unknown name takes the time a known one does. bcrypt spends the cost its hash names:
  // the stand-in has to name the cost padHash's own hashes have - 12 on PHP 8.4 and later -
  // where a fixed cost 10 answered an unknown name in a quarter of the time.

  $dummy = function_exists ( 'padAuthDummyHash' ) ? padAuthDummyHash () : constant ( 'padAuthDummyHash' );

  $same = ( password_get_info ( $dummy ) ['options'] ['cost'] ?? 0 ) === ( password_get_info ( padHash ( 'secret' ) ) ['options'] ['cost'] ?? -1 )
        ? 'the cost of a real hash' : 'another cost';

  $unknown = padAttempt ( 'secret', NULL, NULL ) ? 'in' : 'refused';

?>
