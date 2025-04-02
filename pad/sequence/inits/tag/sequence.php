<?php

  $padSeqInfo ['kinds'] [] = include 'sequence/inits/sequence/info.php';

  if ( $padSeqParm )
    if     ( is_numeric ( $padSeqParm       ) ) return include 'sequence/inits/sequence/integer.php';
    elseif ( strpos     ( $padSeqParm, '..' ) ) return include 'sequence/inits/sequence/range.php';
    elseif ( strpos     ( $padSeqParm, ';'  ) ) return include 'sequence/inits/sequence/list.php';

  foreach ( $padParms [$pad] as $padParmsOne )  {

    if ( $padParmsOne ['padPrmKind'] == 'option' ) {

      $padSeqSeq  = $padParmsOne ['padPrmName']; 
      $padSeqParm = $padParmsOne ['padPrmValue'];

      if     ( file_exists ( "sequence/types/$padSeqSeq" ) ) return include 'sequence/inits/sequence/type.php';
      elseif ( isset ( $padSeqStore [$padSeqSeq] )         ) return include 'sequence/inits/sequence/store.php';

    }

  }

  return include 'sequence/inits/sequence/default.php';

    
?>