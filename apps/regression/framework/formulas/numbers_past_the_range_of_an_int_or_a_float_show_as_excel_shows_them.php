<?php

  // A number of 9.22E+18 or more is shown in General with an exponent - a formula's, a typed
  // one, the digits of a record such as a 22-digit tracking number, which is read as a
  // number - where the plain form cast it to an int and the request ended; a typed 1e400,
  // past the largest number a float holds, stays the text it is, as Excel keeps it, and is
  // no number to VALUE; a number below 2.2251E-308, typed or computed, is 0, as in Excel,
  // where the General and the E+00 forms divided by a power of ten that was 0; a format that
  // scales a number past a float answers #VALUE!.

  $shipments = [ [ 'order' => 'A-1', 'tracking' => '9400100000000000000000' ] ];

?>
