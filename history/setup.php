<?php
  
  if ( ! $pad )
    return;

  $padHstLvl [$pad] = ( $padHstShort ) ? "Tag: $padTypeCheck-$padCnt" : $padCnt;

  $padHstVar = '$padHstRsl';
  
  for ( $padK = 1; $padK < $pad; $padK++ )
    if ( $padHstShort )
      $padHstVar .= "['$padHstLvl[$padK]']" . "['occurrences']"  . "[$padOccur[$padK]]"; 
    else
      $padHstVar .= "['$padHstLvl[$padK]']" . "['occurrences']"  . "[$padOccur[$padK]]" . "['tags']"; 
      
  $padHstVar .= "['$padHstLvl[$pad]']";
 
  eval ( $padHstVar . " = ['result' => ''];" );
  eval ( '$padHstPnt [$pad] = &' . $padHstVar . ';' );
 
?>