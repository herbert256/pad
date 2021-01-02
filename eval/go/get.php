<?php  

  $tag_parms = $GLOBALS['pad_parms_val']

  $name  = $parm;
  $value = '';
  $parm  = [];
  $kind  = $type;
  $count 

  if ( isset ( $tag_parms [2] ) )
    $value = $tag_parms [2];

  foreach ($tag_parms as $k => $v)
    if ( $k > 2 )
      $parm [] = $v;

  $count = count ($parm);

  return include PAD_HOME . "eval/$type.php";w

?>