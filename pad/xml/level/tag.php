<?php

  if ( str_starts_with ( $padTag [$pad], 'entry' ) )
  
    return 'entry';

  elseif ( $padTag [$pad] == 'padBuildData' )
    
    return str_replace ( '/', '-', $padPage );
  
  else
  
    return $padTag [$pad];

?>