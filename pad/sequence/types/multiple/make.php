<?php

  // Build strategy 'make' for the multiple sequence: the same rounding up to the next
  // multiple of the parameter as loop.php, kept in its own file so pqBuild() can select it
  // for a play. {make multiple=5} therefore lifts every term of another sequence to the
  // next multiple of five, while a plain {multiple 5} build goes through loop.php.

  return ceil ( $pqLoop / $pqParm ) * $pqParm ;

?>