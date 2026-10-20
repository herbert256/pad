<?php

  // Build strategy 'fixed' for fermat: the Fermat numbers 2^(2^n) + 1 for n = 0 to 5 - 3, 5, 17,
  // 257, 65537, 4294967297. The next, 2^64 + 1, is past the integer range, so these are all of
  // them a PHP integer holds, and the index of the list settles {keep fermat} and its friends.
  // The first five are prime, and no other Fermat prime is known.

  return [ 3, 5, 17, 257, 65537, 4294967297 ];

?>
