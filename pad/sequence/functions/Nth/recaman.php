<?php

function pad_sequence_recaman($n)
{
  if($n <= 1)
    return 0;

  $s = array();
  array_push($s, 0);

  $prev = 0;
  for ($i = 1; $i < $n; $i++)
  {
    $curr = $prev - $i;

    if($curr < 0 or in_array($curr, $s))
      $curr = $prev + $i;

    array_push($s, $curr);

    $prev = $curr;
  }

  return $curr; 

}

?>