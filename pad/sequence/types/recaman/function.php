<?php


/**
 * Calculates the nth Recamán sequence term.
 *
 * Each term a(n) = a(n-1) - n if positive and not already
 * in sequence, otherwise a(n-1) + n.
 *
 * @param int $n The index in the sequence.
 *
 * @return int The nth Recamán number.
 */
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