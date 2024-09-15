<?php

  if ( $padSeqSetSeq or $padSeqSetStore )
    return include '/pad/sequence/inits/sequence/set.php';

  foreach ( $padOpt [$pad] as $padK => $padSeqParm )
    if ( $padK )
      if     ( strpos ( $padSeqParm, '..' ) ) return include '/pad/sequence/inits/sequence/range.php';
      elseif ( strpos ( $padSeqParm, ';'  ) ) return include '/pad/sequence/inits/sequence/list.php';

  foreach ( $padSeqEntryList as $padSeqSeq => $padSeqParm )
    if ( $padSeqSeq ) 
      if ( isset ( $padSeqStore [$padSeqSeq] ) ) 
        return include '/pad/sequence/inits/sequence/store.php';
      elseif ( file_exists ( "/pad/sequence/types/$padSeqSeq" ) ) 
        return include '/pad/sequence/inits/sequence/type.php';

  return include '/pad/sequence/inits/sequence/loop.php';
 
?>