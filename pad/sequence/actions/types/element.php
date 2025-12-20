<?php

  if ( $pqActionParm )
  $pqResult = [ $pqActionKey => array_combine (
    range (1, count($pqResult)),
    array_values($pqResult)) [$pqActionParm] ];

?>
