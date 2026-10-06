<?php

  // A hash made with PHP's default settings now needs nothing; one made with weaker
  // settings, no hash at all, or something that is no hash needs a new one.

  $rehash = json_encode ( [
    'current' => padHashNeedsRehash ( padHash ( 'secret' ) ),
    'weaker'  => padHashNeedsRehash ( password_hash ( 'secret', PASSWORD_BCRYPT, [ 'cost' => 4 ] ) ),
    'empty'   => padHashNeedsRehash ( '' ),
    'null'    => padHashNeedsRehash ( NULL ),
    'junk'    => padHashNeedsRehash ( 'not a hash' )
  ] );

?>
