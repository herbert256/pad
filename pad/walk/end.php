<?php

  if ( padCloseWithPad () )
    include pad . 'walk/parse_opt.php';

  if ( padXref )
    include pad . 'info/types/xref/items/walk.php';

  $padWalk [$pad] = 'end';

  $padContent = $padResult [$pad];
  $padParm    = $padOpt [$pad] [1] ?? '';
  include pad . "types/" . $padType [$pad] . ".php";
  
  $padResult [$pad] = $padContent;

  include pad . "level/flags.php"; 
  
?>