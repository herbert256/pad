<?php

  if ( ! $padSeqPull ) 

    if ( $padSeqSeq and file_exists ( "sequence/types/$padSeqSeq" ) )
  
      foreach ( $padParms [$pad] as $padTmp )

        if ( $padTmp ['padPrmKind'] == 'option' )

          if ( ! in_array ( $padTmp ['padPrmName'], $padSeqDone ) )

            if ( isset ( $padSeqStore [ $padTmp ['padPrmName'] ] ) ) {

              $padSeqPull  = $padTmp ['padPrmName'];
              $padPrmValue = $padSeqParm;
          
              if     ( padSeqPlay ( $padSeqType ) ) $padSeqPlay = $padSeqType;
              elseif ( padSeqPlay ( $padSeqTag  ) ) $padSeqPlay = $padSeqTag;
              else                                  $padSeqPlay = 'make';

              $padSeqPlaySource = 'inits/pull/find'; 
              return include 'sequence/plays/extra.php';

            }

?>