<?php
  
  if ( isset ($padBuild) ) {
    $padTmp = $padBuild;
    unset ($padBuild);
    return $padTmp;
  }

  return TRUE;

?>