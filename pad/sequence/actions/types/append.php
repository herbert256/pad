<?php

  foreach ( $pqActionList as $pqAppendKey )
    foreach ($pqStore [$pqAppendKey] as $pqAppendValue)
      $pqResult [] = $pqAppendValue;

  return $pqResult;

?>