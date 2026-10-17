<?php

  // The parameter of sequence:eval() written as a quoted string is an expression the template
  // writes, and runs - the third term of '@ * 2' is 6. It was refused as one from a value,
  // which sequence:eval(3, $q) still is.

?>
