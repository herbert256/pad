<?php

  // url - An address of the web at example.com, .org or .net, the domains kept for
  // examples, with a path of one or two words: https://www.example.org/dolor/magna.

  $site = 'https://www.' . padFakePick ( [ 'example.com', 'example.org', 'example.net' ] );

  return $site . '/' . implode ( '/', array_map ( fn () => padFakeWord (), range ( 1, padFakeNumber ( 1, 2 ) ) ) );

?>
