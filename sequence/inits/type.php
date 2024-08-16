<?php

  if     ( file_exists ( "$padSeqType/order.php")    ) $padSeqBuild = 'order';
  elseif ( file_exists ( "$padSeqType/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( file_exists ( "$padSeqType/jump.php")     ) $padSeqBuild = 'jump';
  elseif ( file_exists ( "$padSeqType/function.php") ) $padSeqBuild = 'function';
  elseif ( file_exists ( "$padSeqType/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( file_exists ( "$padSeqType/make.php")     ) $padSeqBuild = 'make';
  elseif ( file_exists ( "$padSeqType/bool.php")     ) $padSeqBuild = 'bool';
  elseif ( file_exists ( "$padSeqType/filter.php")   ) $padSeqBuild = 'filter';
  else                                               $padSeqBuild = 'none';

?>