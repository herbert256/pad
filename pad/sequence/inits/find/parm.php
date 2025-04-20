<?php

  if ( ! $pqFindParm )
    return;

      if ( $pqSeq    and file_exists("sequence/types/$pqSeq/flags/parm")  ) $pqParm       = $pqFindParm;
  elseif ( $pqAction and file_exists("sequence/actions/double/$pqAction") ) $pqActionParm = $pqFindParm;
  elseif ( $pqAction and file_exists("sequence/actions/parm/$pqAction")   ) $pqActionParm = $pqFindParm;
  elseif ( $pqSeq                                                         ) $pqParm       = $pqFindParm;
  elseif ( $pqAction                                                      ) $pqActionParm = $pqFindParm;
 
?>