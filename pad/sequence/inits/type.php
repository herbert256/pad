<?php

  if     ( padExists ( "$padSeqType/order.php")    ) $padSeqBuild = 'order';
  elseif ( padExists ( "$padSeqType/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( padExists ( "$padSeqType/jump.php")     ) $padSeqBuild = 'jump';
  elseif ( padExists ( "$padSeqType/function.php") ) $padSeqBuild = 'function';
  elseif ( padExists ( "$padSeqType/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( padExists ( "$padSeqType/make.php")     ) $padSeqBuild = 'make';
  elseif ( padExists ( "$padSeqType/bool.php")     ) $padSeqBuild = 'bool';
  elseif ( padExists ( "$padSeqType/filter.php")   ) $padSeqBuild = 'filter';
  else                                               $padSeqBuild = 'none';

?>