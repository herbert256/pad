<?php

  // Precomputed term cache for the motzkin sequence.
  //
  // const PADmotzkin holds its first 44 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADmotzkin[$pqLoop-1]) and by
  // sequence/build/check.php (membership through an index of it, for a type without a
  // test of its own), so those paths answer from the table instead of recomputing the
  // term. Data only - no logic here.

  const PADmotzkin = [1,2,4,9,21,51,127,323,835,2188,5798,15511,41835,113634,310572,853467,2356779,6536382,18199284,50852019,142547559,400763223,1129760415,3192727797,9043402501,25669818476,73007772802,208023278209,593742784829,1697385471211,4859761676391,13933569346707,40002464776083,114988706524270,330931069469828,953467954114363,2750016719520991,7939655757745265,22944749046030949,66368199913921497,192137918101841817,556704809728838604,1614282136160911722,4684478925507420069];

?>
