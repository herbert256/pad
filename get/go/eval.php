<?php  

  $tag_parms = $GLOBALS['pad_parms_val']

  $name  = $parm;
  $value = '';
  $parm  = [];
  $kind  = $type;

  if ( isset ( $tag_parms [2] ) )
    $value = $tag_parms [2];

  $pad_parms_val = [];
  foreach ($tag_parms as $k => $v)
    if ( $k > 2 )
      $pad_parms_val [] = $v;

  $count = count ($pad_parms_val);

  return include PAD_HOME . "eval/$type.php";w

?>