<?php

  // alphanum - letters and digits only, of any script: a user name, a code without signs.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^[\pL\pM\pN]+$/uD', $value ) === 1,
    'message' => ':label must contain letters and digits only'
  ];

?>
