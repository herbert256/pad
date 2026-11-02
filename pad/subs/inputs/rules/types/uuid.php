<?php

  // uuid - a UUID in its usual form, 8-4-4-4-12 hexadecimal digits, of any version - what
  // padStrUuid makes - in either case.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/iD', $value ) === 1,
    'message' => ':label must be a UUID'
  ];

?>
