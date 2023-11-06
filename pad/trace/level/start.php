<?php

  $padTraceLevelChilds  [$pad] = 0;
  $padTraceOccurChilds  [$pad] = [];
  $padTraceOccurWritten [$pad] = [];

  padTraceSet ( 'level', 'start' );

  if ( $padTraceStartLvl )
    padTrace ( 'level', 'start', 
      ' tag='  . $padTag     [$pad] . 
      ' type=' . $padType    [$pad] . 
      ' pair=' . $padPair    [$pad] . 
      ' parm=' . $padPrmType [$pad]
    );

  include pad . 'trace/items/data.php';      
  include pad . 'trace/items/flags.php';      

  include pad . 'trace/items/content.php';      
  include pad . 'trace/items/true.php';      
  include pad . 'trace/items/false.php';      
  include pad . 'trace/items/base.php';      

?>