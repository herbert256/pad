<?php

  include PAD . 'level/setup.php';

  include PAD . 'level/parms/parms.php';
  include PAD . 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include PAD . "options/else.php";
  if ( padTagParm ('data') ) $padData [$pad] = include PAD . "options/data.php";

  include PAD . 'level/set.php';

  $padTry = 'level/go';
  include PAD . 'try/try.php';

  if ( $padNextPadLevel )
    return include PAD . 'level/nextLevel.php';

  include PAD . 'level/base.php';
  include PAD . 'level/pipes/before.php';
  include PAD . 'level/data.php';
  include PAD . 'level/name.php';
  include PAD . 'handling/handling.php';

  if ( padTagParm ('dump') )
    include PAD . 'options/dump.php';

  if ( count ( $padOptionsAppStart [$pad] ) )
    include PAD . 'options/go/app.php';

  include PAD . 'options/go/start.php';

  if ( isset ( $padPrm [$pad] ['callback'] ) )
    include PAD . 'level/callback.php' ;

  if ( padOpenCloseOk ( $padBase[$pad], '@end@') )
    include PAD . 'level/start_end/end1.php';

  if ( $padInfo )
    include PAD . 'events/levelStart.php';

  if ( padOpenCloseOk ( $padBase[$pad], '@start@') )
    return include PAD . 'level/start_end/start1.php';

  if ( count ( $padData [$pad] ) and $padBase [$pad] )
    include PAD . 'occurrence/occurrence.php';

?>