<?php

  if ( $pqName )
    return;

      if ( $pqPush     and $pqPush     !== TRUE ) $pqName = $pqPush;           
  elseif ( $pqPull     and $pqPull     !== TRUE ) $pqName = $pqPull;           
  elseif ( $pqPullName and $pqPullName !== TRUE ) $pqName = $pqPullName;           
  elseif ( file_exists ( "sequence/types/$pqSeq" )  ) $pqName = $pqSeq;
  else                                                    $pqName = 'sequence'; 
  
  $padName [$pad] = $pqName;

?>