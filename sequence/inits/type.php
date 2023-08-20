<?php

  $padSeqChk = "$padSeqType";

  if     ( padExists ( "$padSeqChk/order.php")    ) $padSeqBuild = 'order';
  elseif ( padExists ( "$padSeqChk/fixed.php")    ) $padSeqBuild = 'fixed';
  elseif ( padExists ( "$padSeqChk/jump.php")     ) $padSeqBuild = 'jump';
  elseif ( padExists ( "$padSeqChk/function.php") ) $padSeqBuild = 'function';
  elseif ( padExists ( "$padSeqChk/loop.php")     ) $padSeqBuild = 'loop';
  elseif ( padExists ( "$padSeqChk/make.php")     ) $padSeqBuild = 'make';
  elseif ( padExists ( "$padSeqChk/bool.php")     ) $padSeqBuild = 'bool';
  elseif ( padExists ( "$padSeqChk/filter.php")   ) $padSeqBuild = 'filter';
    
  else padError ("No type definition found for '$padSeqSeq'");

?>