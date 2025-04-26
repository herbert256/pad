<?php

  if ( ! $pqParm )
    $pqParm = $padParm;

  $pqDone [] = 'increment';

  if ( ! str_contains ( $pqParm, '..' ) )
    $pqParm = "$pqFrom..$pqTo";

  return padGetRange ( $pqParm,  $pqInc );

?>