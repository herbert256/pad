<?php


  function padLevel ( $value ) {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] ) 
                   . $value  
                   . substr ( $padOut [$pad], $padEnd [$pad]+1 );
    
  }


  function padLevelNo ( $no ) {

    padLevel ( "&open;$no&close;" );
    
  }


  function padLevelNoSingle () {

    padLevelNo ( $GLOBALS ['padBetween'] );

  }

  
  function padLevelNoPair () {

    global $padOut, $padStart, $padEnd, $pad;

    padLevelNo ( substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 ) );
    
  }


  function padLevelBetween () {

    global $padOut, $padStart, $padEnd, $pad, $padBetween, $padOrgSet;

    $padBetween = substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );

    $padOrgSet = $padBetween;
    
  }


  function padLevelNoOpen () {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

  }


  function padLevelStart () {

    global $padOut, $padStart, $padEnd, $pad;

    $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen ( $padOut [$pad] ) );

  }


  function padLevelEnd () {

    global $padOut, $padStart, $padEnd, $pad;

    $padEnd [$pad] = strpos ( $padOut [$pad], '}' );;

  }


?>