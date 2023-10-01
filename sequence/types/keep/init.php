<?php

  $padSeqFilter = $padSeqFilterType = [];

  foreach ( $padPrm [$pad] as $padSeqFilterName => $padSeqFilterVal )

    if ( $padSeqFilterName <> $padSeqSeq ) {

      $padSeqFilterTmp = padSeqFilterType ( $padSeqFilterName );

      if ( $padSeqFilterTmp <> 'none' ) {

        $padSeqFilter     [$padSeqFilterName] = $padSeqFilterVal;
        $padSeqFilterType [$padSeqFilterName] = $padSeqFilterTmp;

        if ( $padSeqFilterTmp == 'bool' or $padSeqFilterTmp == 'function' ) 
          include_once "$padSeqTypes/$padSeqFilterName/$padSeqFilterTmp.php";

        if ( isset ( $padSeqOprGo [$padSeqFilterName] ) )
          unset ( $padSeqOprGo [$padSeqFilterName] );

      }

    }

?>