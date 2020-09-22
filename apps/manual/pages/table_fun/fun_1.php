<?php

  for ($row = 1; $row <= 3; $row++) {
    for ($col = 1; $col <= 5; $col++) {     
      $rows [$row] ['cols'] [$col] ['value'] = ($row*10) + $col;
    }
  }

?>