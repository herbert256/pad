<?php

  $padSeqInfo ['kinds'] [] = include 'sequence/inits/sequence/info.php';

  if ( $padSeqPullName and isset ( $padSeqStore [$padSeqPullName] ) )
    return include 'sequence/inits/sequence/pull.php';

  foreach ( $padParms [$pad] as $padParmsOne )  {

    $padSeqSeq  = $padParmsOne ['padPrmName']; 
    $padSeqParm = $padParmsOne ['padPrmValue'];

    if ( $padParmsOne ['padPrmKind'] == 'parm' ) {

      if     ( is_numeric ( $padSeqParm       ) ) return include 'sequence/inits/sequence/integer.php';
      elseif ( strpos     ( $padSeqParm, '..' ) ) return include 'sequence/inits/sequence/range.php';
      elseif ( strpos     ( $padSeqParm, ';'  ) ) return include 'sequence/inits/sequence/list.php';

    } elseif ( $padParmsOne ['padPrmKind'] == 'option' ) {  

      if ( in_array ( $padSeqSeq, $padSeqDone ) ) 
        continue;

      if ( $padSeqSeq == 'from'      ) return include 'sequence/inits/sequence/basic.php';
      if ( $padSeqSeq == 'to'        ) return include 'sequence/inits/sequence/basic.php';
      if ( $padSeqSeq == 'rows'      ) return include 'sequence/inits/sequence/basic.php';
      if ( $padSeqSeq == 'increment' ) return include 'sequence/inits/sequence/basic.php';

      if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return include 'sequence/inits/sequence/type.php';
      elseif ( isset ( $padSeqStore [$padSeqSeq] )         ) return include 'sequence/inits/sequence/store.php';

      if ( file_exists ( "sequence/actions/types/$padSeqSeq.php" ) ) {
        padSplit ( '|', $padSeqParm, $padSeqParm1, $padSeqParm2 );
        if ( isset ( $padSeqStore [$padSeqParm1] ) )
          return include 'sequence/inits/sequence/action.php';
      }

    }

  }

  return include 'sequence/inits/sequence/default.php';
    
?>