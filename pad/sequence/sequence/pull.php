<?php

  // Direct read of a stored sequence: resets the subsystem's state and hands back
  // $pqStore [$name] untouched.
  //
  // Nothing in the engine includes this file - every pull form goes through sequence/tag.php
  // or sequence/action.php 

  global $pqStore;

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'exits/info.php';

  return $pqStore [ $padName [$pad] ];

?>