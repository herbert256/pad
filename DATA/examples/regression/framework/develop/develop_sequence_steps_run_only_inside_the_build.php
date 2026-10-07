<?php

  // The step files of develop's sequence build - basic, flags, keepRemoveFlag, play ... - are
  // pages too, and asked on their own they took $type from the request: ?sequence/basic&go=1
  // &type=../../../DATA/x wrote x.pad there, a template of the asker's text anywhere, and
  // ?sequence/flags emptied the flags/ directory a type named. They run inside the build
  // only, whose types are the engine's own directory names. Both are aimed under DATA/ here,
  // and put back.

  $seqProbe = DATA . 'zz-sequence-probe';

  @mkdir ( "$seqProbe/flags", 0755, TRUE );
  file_put_contents ( "$seqProbe/flags/keep.txt", 'kept' );

  padCurl ( $padHost . 'develop/?sequence/basic&go=1&parm=4&type=../../../DATA/zz-sequence-probe/page' );
  padCurl ( $padHost . 'develop/?sequence/flags&go=1&type=../../../DATA/zz-sequence-probe' );

  $answer = 'page written: ' . ( file_exists ( "$seqProbe/page.pad" ) ? 'yes' : 'no' )
          . ', flags kept: '   . ( file_exists ( "$seqProbe/flags/keep.txt" ) ? 'yes' : 'no' );

  padDeleteDataDir ( $seqProbe );

  unset ( $seqProbe );

?>
