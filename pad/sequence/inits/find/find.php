<?php

      if ( padSeqPlay ( $padSeqTag ) or padSeqPlay ( $padSeqType ) ) $padSeqFindType = 'play';
  elseif ( $padSeqTag == 'sequence'  or $padSeqType == 'sequence'  ) $padSeqFindType = 'sequence';
  elseif ( $padSeqTag == 'action'    or $padSeqType == 'action'    ) $padSeqFindType = 'action';
  elseif ( $padSeqTag == 'pull'      or $padSeqType == 'pull'      ) $padSeqFindType = 'pull';
  elseif ( $padSeqTag == 'continue'  or $padSeqType == 'continue'  ) $padSeqFindType = 'continue';
  else                                                               $padSeqFindType = '???';

  $padSeqFindParm = $padOpt [$pad] [1];

  include 'sequence/inits/find/prefix.php';
  include 'sequence/inits/find/tag.php';

  if ( ! $padSeqPull                                   ) include 'sequence/inits/find/pull.php';
  if ( ! $padSeqPull and $padSeqPullName               ) $padSeqPull = $padSeqPullName;
  if ( ! $padSeqPull and $padSeqFindType == 'continue' ) $padSeqPull = $padLastPush;
  if ( ! $padSeqPull and $padSeqFindType == 'pull'     ) $padSeqPull = $padLastPush;

  if ( ! $padSeqPull and  $padSeqSeq and ($padSeqFindType == 'pull' or $padSeqFindType == 'continue' ) ) {
    
    $padPrmName  = $padSeqSeq;
    $padPrmValue = $padSeqParm;

    include 'sequence/plays/add.php';

    $padSeqSeq  = 'loop';
    $padSeqParm = '';

  } 


  if ( ! $padSeqSeq )
    include 'sequence/inits/find/parm.php';

  if ( ! $padSeqSeq    and $padSeqFindType == 'play'     ) include 'sequence/inits/find/sequence.php';
  if ( ! $padSeqSeq    and $padSeqFindType == 'sequence' ) include 'sequence/inits/find/sequence.php';
  if ( ! $padSeqAction and $padSeqFindType == 'action'   ) include 'sequence/inits/find/action.php';

  if ( $padSeqFindParm and $padSeqSeq and file_exists("sequence/types/$padSeqSeq/flags/parm") ) { 
    $padSeqParm     = $padSeqFindParm;
    $padSeqFindParm = '';
  }  elseif ( $padSeqFindParm and $padSeqAction and file_exists("sequence/actions/double/$padSeqAction") ) {
    $padSeqActionParm = $padSeqFindParm;
    $padSeqFindParm   = '';
  } elseif ( $padSeqFindParm and $padSeqAction and file_exists("sequence/actions/parm/$padSeqAction") ) {
    $padSeqActionParm = $padSeqFindParm;
    $padSeqFindParm   = '';
  }  

  if ( ! $padSeqSeq and $padSeqFindParm and is_numeric ( $padSeqFindParm ) ) {
    $padSeqSeq      = 'loop';
    $padSeqRows     = $padSeqFindParm;
    $padSeqFindParm = '';
  }

  if ( $padSeqAction and $padSeqFindParm ) {
    $padSeqActionParm = $padSeqFindParm;
    $padSeqFindParm   = '';
  }

  if ( ! $padSeqPull and ! $padSeqSeq ) {
    $padSeqSeq   = 'loop';
    $padSeqBuild = 'loop'; 
  }

  if ( $padSeqPull and $padSeqSeq ) {

    $padPrmName  = $padSeqSeq;
    $padPrmValue = $padSeqParm;

    include 'sequence/plays/add.php';

    $padSeqSeq  = '';
    $padSeqParm = '';

  } 

?>