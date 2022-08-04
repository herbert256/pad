<?php

  pad_done (, $pad_seq_seq,  TRUE );
  pad_done (, $pad_seq_name, TRUE );
  pad_done (, 'from',        TRUE );
  pad_done (, 'increment',   TRUE );
  pad_done (, 'to',          TRUE );
  pad_done (, 'rows',        TRUE );
  pad_done (, 'min',         TRUE );
  pad_done (, 'max',         TRUE );
  pad_done (, 'unique',      TRUE );
  pad_done (, 'page',        TRUE );
  pad_done (, 'start',       TRUE );
  pad_done (, 'end',         TRUE );
  pad_done (, 'low',         TRUE );
  pad_done (, 'high',        TRUE );
  pad_done (, 'random',      TRUE );
  pad_done (, 'push',        TRUE );
  pad_done (, 'store',       TRUE );
  pad_done (, 'pull',        TRUE );
  pad_done (, 'sequence',    TRUE );
  pad_done (, 'range',       TRUE );
  pad_done (, 'protect',     TRUE );
  pad_done (, 'keep',        TRUE );
  pad_done (, 'remove',      TRUE );
  pad_done (, 'make',        TRUE );

  foreach ( $pad_prms_tag as $pad_seq_tag_name => $pad_seq_tag_value ) {

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/make.php" ) )
      pad_done (, $pad_seq_tag_name, TRUE );

    if ( file_exists ( PAD . "sequence/types/$pad_seq_tag_name/filter.php" ) )
      pad_done (, $pad_seq_tag_name, TRUE );

  }

?>