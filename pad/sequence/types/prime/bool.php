<?php

  function pad_sequence_prime ( $num ) {

    if ($num == 1)
      return FALSE;

    for ($i = 2; $i <= $num/2; $i++)
      if ($num % $i == 0)
        return FALSE;

    return TRUE;

  }

?>