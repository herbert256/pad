<?php

  if ( ! $pqFindParm )
    return;

      if ( $pqSeq    and file_exists ( PT . "$pqSeq/flags/parm")  ) $pqParm       = $pqFindParm;
  elseif ( $pqAction and file_exists ( PQ . "actions/double/$pqAction") ) $pqActionParm = $pqFindParm;
  elseif ( $pqAction and file_exists ( PQ . "actions/parm/$pqAction")   ) $pqActionParm = $pqFindParm;
  elseif ( $pqSeq                                                         ) $pqParm       = $pqFindParm;
  elseif ( $pqAction                                                      ) $pqActionParm = $pqFindParm;

?>
