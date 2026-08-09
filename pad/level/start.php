<?php

  // Opens and runs a level for a tag whose type has been resolved - the spine of the
  // pipeline.
  //
  // In order: level/setup.php claims a new $pad slot, level/parms/ parses the parameters,
  // level/split.php cuts off an @else@ branch, the else= and data= options are applied and
  // $name= assignments are published as globals; then the tag handler runs guarded by
  // try/try.php ($padTry = 'level/go'). Afterwards the level's content and iteration data
  // are settled (base, before-pipe, data, name and handling/), the dump and app option
  // hooks fire, a callback= is initialised and an @end@ section is put aside. Finally
  // occurrence/occurrence.php renders the first occurrence - unless an @start@ section
  // defers the body, or a {break}/{continue} asked to jump to an outer level.

  include PAD . 'level/setup.php';

  include PAD . 'level/parms/parms.php';
  include PAD . 'level/split.php';

  if ( padTagParm ('else') ) $padFalse       = include PAD . "options/else.php";
  if ( padTagParm ('data') ) $padData [$pad] = include PAD . "options/data.php";

  include PAD . 'level/set.php';

  include PAD . 'try/level/go.php';

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

  // The base is tested against '' rather than for truth: a level whose whole template is the
  // single character 0 is falsy in PHP, and testing it for truth skipped the occurrence
  // altogether, so the level rendered nothing.

  if ( count ( $padData [$pad] ) and $padBase [$pad] !== '' )
    include PAD . 'occurrence/occurrence.php';

?>