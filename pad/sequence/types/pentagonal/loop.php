<?php

  // Loop build for pentagonal: (3n^2 - n)/2, the nth pentagonal number, so 1, 5, 12, 22,
  // 35, 51, 70, ...

  return pqProduct ( [ $pqLoop, 3 * $pqLoop - 1 ], 2 );

?>
