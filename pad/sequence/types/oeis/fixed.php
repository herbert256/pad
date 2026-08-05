<?php

  // Fixed build for oeis: hands back a whole OEIS sequence as its term list, the one whose
  // A-number is the parameter - {sequence oeis=45} for A000045, the Fibonacci numbers.
  //
  // Reads the generated table in oeis/oeis.php, which is indexed by A-number with the
  // leading zeroes dropped. Reached with build=fixed or through a pull; the default for
  // this type is make.php, which pqBuild() ranks higher.

  include_once PT . 'oeis/oeis.php';

  return OEIS [$pqParm];

?>