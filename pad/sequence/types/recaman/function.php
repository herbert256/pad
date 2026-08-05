<?php

  // Function build for recaman: pqRecaman($n) is the nth term of Recaman's sequence, which
  // starts at 0 and at step i steps back by i when that lands on a new non-negative value
  // and forward by i otherwise - 0, 1, 3, 6, 2, 7, 13, 20, 12, 21, 11, 22, 10, ...
  //
  // Nothing is kept between calls, so each term rebuilds the whole history and searches it
  // with in_array; generating m terms costs on the order of m^3. Membership tests take the
  // cheaper route through the precomputed table in generated.php.

function pqRecaman($n)
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