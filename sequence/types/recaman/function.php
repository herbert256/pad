<?php

function padSeqRecaman($n)
{
  if($n <= 1)
    return 0;

  $s = array();
  array_push($s, 0);

  $padrev = 0;
  for ($i = 1; $i < $n; $i++)
  {
    $curr = $padrev - $i;

    if($curr < 0 or in_array($curr, $s))
      $curr = $padrev + $i;

    array_push($s, $curr);

    $padrev = $curr;
  }

  return $curr; 

}

?>