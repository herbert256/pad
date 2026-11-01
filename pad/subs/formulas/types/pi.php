<?php

  // pi - PI() is the number pi, to 15 digits 3.14159265358979.
  //
  //   =PI()                         3.141592654
  //   =ROUND(PI()*2, 4)             6.2832

  return [
    'group' => 'Math',
    'args'  => [ 0, 0 ],
    'call'  => fn ( $args ) => M_PI
  ];

?>
