<?php
    
  if ( ! $GLOBALS [ "padSeq" . ucfirst($padSeqCheck) ] )
    return;

  foreach ( $padSeqPlays as $padSeqPlay )
    if ( $padSeqPlay ['padSeqPlay'] == $padSeqCheck )
      return;
         
  if ( in_array ( $padSeqBuild, ['start','store','pull'] ) )

      foreach ( $padSeqPlays as $padK => $padV )

        if ( $padV ['padSeqPlay'] == 'make' ) {

          $padSeqPlays [$padK] ['padSeqBuild'] = padSeqBuild ( $padV ['padSeqSeq'], $padSeqCheck );
          $padSeqPlays [$padK] ['padSeqPlay']  = $padSeqCheck;

          return;

        }

?>