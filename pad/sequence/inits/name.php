<?php

  // Decides the variable name the sequence's values are published under, in order of
  // preference: an explicit name=, the push or pull store name, the sequence type itself,
  // and finally 'sequence'.
  //
  // That third case is what makes {prime}{$prime}{/prime} work. The answer is handed to the
  // level as $padName [$pad], which occurrence/ uses when it binds each value.

      if ( $pqName                              ) return;
  elseif ( $pqPush     and $pqPush     !== TRUE ) $pqName = $pqPush;
  elseif ( $pqPull     and $pqPull     !== TRUE ) $pqName = $pqPull;
  elseif ( pqSeq ( $pqSeq )                     ) $pqName = $pqSeq;
  else                                            $pqName = 'sequence';

  $padName [$pad] = $pqName;

?>