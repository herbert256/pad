<?php

      if ( $pqName                              ) return;
  elseif ( $pqPush     and $pqPush     !== TRUE ) $pqName = $pqPush;
  elseif ( $pqPull     and $pqPull     !== TRUE ) $pqName = $pqPull;
  elseif ( pqSeq ( $pqSeq )                     ) $pqName = $pqSeq;
  else                                            $pqName = 'sequence';

  $padName [$pad] = $pqName;

?>
