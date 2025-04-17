<?php

  if ( count ( $pqActionList ) == 1 )
    array_splice (
      $pqResult,
      $pqActionList [0]
    );
  elseif ( count ( $pqActionList ) == 2 )
    array_splice (
      $pqResult,
      $pqActionList [0],
      $pqActionList [1]
    );
  elseif ( count ( $pqActionList ) == 3 )
    array_splice (
      $pqResult,
      $pqActionList [1],
      $pqActionList [2],
      $pqActionList [0]
    );

  return $pqResult;

?>