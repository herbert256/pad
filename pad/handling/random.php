<?php
  
  if ( $padHandCnt >= count ( $padData [$pad] ) )
    return include 'handling/shuffle.php';

  if ( $padHandCnt == 1) {
    $padData [$pad] = [ 1 => $padData [$pad] [ array_rand ( $padData [$pad] ) ] ];
    return;
  }

  $padHandRand = array_rand ( $padData [$pad], $padHandCnt );
  $padHandData = $padData [$pad];

  $padData [$pad] = [];
  
  foreach ( $padHandRand as $padHandKey )
    $padData [$pad] [] = $padHandData [$padHandKey];

  include 'handling/shuffle.php';
  
?>