<?php

  // today - TODAY() is the serial of today, in the time zone of the application (padNow, which
  // padNowFreeze holds still for a test).
  //
  //   =TODAY()=INT(NOW())           TRUE

  return [
    'group' => 'Date and time',
    'args'  => [ 0, 0 ],
    'call'  => fn ( $args ) => padFormulaToday ( FALSE )
  ];

?>
