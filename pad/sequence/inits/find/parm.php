<?php

  if ( ! $padSeqFindParm )
    return;

      if ( $padSeqSeq    and file_exists("sequence/types/$padSeqSeq/flags/parm")  ) $padSeqParm       = $padSeqFindParm;
  elseif ( $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) $padSeqActionParm = $padSeqFindParm;
  elseif ( $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction")   ) $padSeqActionParm = $padSeqFindParm;
  elseif ( $padSeqSeq                                                             ) $padSeqParm       = $padSeqFindParm;
  elseif ( $padSeqAction                                                          ) $padSeqActionParm = $padSeqFindParm;
 
?>