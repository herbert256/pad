<?php
  
  if ( ! $pad )
    return;

  $padLogVar = '$padLogRsl';
  
  for ( $padK = 0; $padK < $pad; $padK++ )
    $padLogVar .= "[$padLogLvl[$padK]]" . "['occurrences']"  . "[$padOccur[$padK]]" . "['tags']"; 
      
  $padLogVar .= "[$padCnt]";
  $padLogLvl [$pad] = $padCnt;
 
  eval ( $padLogVar . '= [];' );
  eval ( '$padLogPnt [$pad] = &' . $padLogVar . ';' );

  $padLogNow = &$padLogPnt [$pad];
 
  $padLogNow ['base']   = '';
  $padLogNow ['result'] = '';

?>