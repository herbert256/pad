<?php

  // alpha - letters only, of any script, with the marks that go with them: José and Zoë
  // pass, a space, a digit or a dash does not.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^[\pL\pM]+$/uD', $value ) === 1,
    'message' => ':label must contain letters only'
  ];

?>
