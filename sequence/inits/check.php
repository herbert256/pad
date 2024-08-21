<?php

  $padSeqCheck = FALSE;

  if ( $padSeqRows                       ) return;
  if ( $padSeqTo <> PHP_INT_MAX          ) return;
  if ( $padSeqFixed !== FALSE            ) return;
  if ( isset ( $padPrm [$pad] ['try'] )  ) return;
  if ( isset ( $padPrm [$pad] ['most'] ) ) return;
  if ( $padSeqPull                       ) return;
  if ( $padSeqSeq == 'pull' )              return;

  $padSeqCheck = TRUE;

?>