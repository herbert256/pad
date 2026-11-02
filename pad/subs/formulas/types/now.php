<?php

  // now - NOW() is the serial of this moment, the time of day its fraction - to the second.
  //
  //   =NOW()-TODAY()<1              TRUE

  return [
    'group' => 'Date and time',
    'args'  => [ 0, 0 ],
    'call'  => fn ( $args ) => padFormulaToday ( TRUE )
  ];

?>
