<?php

  // Build strategy 'fixed' for automorphic: the numbers whose square ends in the number itself
  // - 76^2 = 5776. Past 1 there are at most two of every length, one ending in 5 and one in 6,
  // the powers 5^(2^k) and 1 - 5^(2^k) modulo 10^d, so the list is all of them that a PHP
  // integer holds - 36, made exactly with whole numbers of any size - and the index of it
  // settles {keep automorphic} and its friends.

  return [ 0, 1, 5, 6, 25, 76, 376, 625, 9376, 90625, 109376, 890625, 2890625, 7109376, 12890625,
           87109376, 212890625, 787109376, 1787109376, 8212890625, 18212890625, 81787109376,
           918212890625, 9918212890625, 40081787109376, 59918212890625, 259918212890625,
           740081787109376, 3740081787109376, 6259918212890625, 43740081787109376,
           56259918212890625, 256259918212890625, 743740081787109376, 2256259918212890625,
           7743740081787109376 ];

?>
