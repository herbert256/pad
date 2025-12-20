<?php

   foreach ( $pqActionList as $pqPrependKey ) {
    $pqPrependReverse = array_reverse($pqStore [$pqPrependKey]);
    foreach ($pqPrependReverse as $pqPrependValue)
      array_unshift ($pqResult, $pqPrependValue);
  }

?>
