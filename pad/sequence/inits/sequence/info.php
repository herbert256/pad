<?php

  if ( $padSeqPullName and isset ( $padSeqStore [$padSeqPullName] ) )
    return 'pull';

  foreach ( $padParms [$pad] as $padParmsOne )  {

    $padSeqInfSeq  = $padParmsOne ['padPrmName']; 
    $padSeqInfParm = $padParmsOne ['padPrmValue'];

    if ( $padParmsOne ['padPrmKind'] == 'parm' ) {

      if     ( is_numeric ( $padSeqInfParm       ) ) return 'integer';
      elseif ( strpos     ( $padSeqInfParm, '..' ) ) return 'range';
      elseif ( strpos     ( $padSeqInfParm, ';'  ) ) return 'list';

    } elseif ( $padParmsOne ['padPrmKind'] == 'option' ) {

      if ( $padSeqInfSeq == 'from'      ) return 'basic';
      if ( $padSeqInfSeq == 'to'        ) return 'basic';
      if ( $padSeqInfSeq == 'rows'      ) return 'basic';
      if ( $padSeqInfSeq == 'increment' ) return 'basic';

      if     ( file_exists ( "sequence/types/$padSeqInfSeq" ) ) return 'type';
      elseif ( isset ( $padSeqStore [$padSeqInfSeq] )         ) return 'store';

      if ( file_exists ( "sequence/actions/types/$padSeqInfSeq.php" ) ) {
        padSplit ( '|', $padSeqInfParm, $padSeqInfParm1, $padSeqInfParm2 );
        if ( isset ( $padSeqStore [$padSeqInfParm1] ) )
          return 'action';
      }

    }

  }

  return 'default';
    
?>