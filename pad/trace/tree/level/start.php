<?php
  
  $padTreeId++;

  $padTreeLevel [$pad] = $padTreeLvl = $padTreeId;

  $padTreeTag = ( $padTag [$pad] == 'padBuildData' ) ? $padPage : $padTag [$pad];

  $padTree [$padTreeLvl] ['tag']  = str_replace ( '/', '-', $padTreeTag );
  $padTree [$padTreeLvl] ['level']  = $pad;
  $padTree [$padTreeLvl] ['occurs'] = [];
  $padTree [$padTreeLvl] ['parm']   = $padOpt  [$pad] [0];
  $padTree [$padTreeLvl] ['type']   = $padType [$pad];
  $padTree [$padTreeLvl] ['childs'] = 0;
  $padTree [$padTreeLvl] ['size']   = 0;

  if ( $pad > 0 ) {

    $padTreeParent    = $padTreeLevel [$pad-1];
    $padTreeParentOcc = $padOccur [$pad-1];
    
    $padTree [$padTreeParent] ['childs'] ++;

    if ( $padTreeParentOcc > 0 and $padTreeParentOcc < 99999 )
      $padTree [$padTreeParent] ['occurs'] [$padTreeParentOcc] ['childs'] ++;

  }

  $padTree [$padTreeLvl] ['result'] = '';
  $padTree [$padTreeLvl] ['source'] = '';
  
  $padEventType = 'level-start';
  include pad . 'trace/tree/event.php';

?>