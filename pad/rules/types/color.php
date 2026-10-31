<?php

  // color - a colour written in hexadecimal, #rgb or #rrggbb, in either case: what
  // <input type="color"> posts, and what a style sheet can be handed back as it is.

  return [
    'check'   => fn ( $value ) => preg_match ( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/iD', $value ) === 1,
    'message' => ':label must be a colour'
  ];

?>
