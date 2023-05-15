<?php

  $padSeqChk = pad . "sequence/types/$padSeqSeq";

  if     ( padExists ( "$padSeqChk/order.php")    ) $padSeqBuild = 'order';
  elseif ( padExists ( "$padSeqChk/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( padExists ( "$padSeqChk/bool.php")     ) $padSeqBuild = 'bool';
  elseif ( padExists ( "$padSeqChk/function.php") ) $padSeqBuild = 'function';
  elseif ( padExists ( "$padSeqChk/main.php")     ) $padSeqBuild = 'main';
  elseif ( padExists ( "$padSeqChk/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( padExists ( "$padSeqChk/make.php")     ) $padSeqBuild = 'make';
  elseif ( padExists ( "$padSeqChk/filter.php")   ) $padSeqBuild = 'filter';
  
  else padError ("No type definition found for '$padSeqSeq'");

?>