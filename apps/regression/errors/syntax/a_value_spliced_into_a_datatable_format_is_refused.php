<?php

  // A value spliced into the format= of {datatable} is part of the pipe expression run for
  // every cell, and is refused: the request value php:abs(-3) ran, the cell showing 3.

  $f = 'php:abs(-3)';

?>
