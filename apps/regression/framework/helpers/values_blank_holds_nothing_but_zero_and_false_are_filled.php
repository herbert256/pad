<?php

  // padBlank: NULL, '' and whitespace (a non-breaking space too), an empty array and an
  // empty Countable or Stringable hold nothing; 0, '0', 0.0, FALSE, TRUE and every other
  // value are filled. padFilled is the opposite, value for value.

  $values = [ NULL, '', '   ', "\t\n", "\u{00A0}\u{2003}", [], new ArrayObject ( [] ),
              new class { function __toString () { return ' '; } },
              0, '0', 0.0, FALSE, TRUE, 'a', ' a ', [ 0 ], [ NULL ], new ArrayObject ( [ 1 ] ),
              new stdClass, "\xFF" ];

  $blank  = json_encode ( array_map ( 'padBlank',  $values ) );
  $filled = json_encode ( array_map ( 'padFilled', $values ) );

?>
