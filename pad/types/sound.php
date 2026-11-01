<?php

  // Type handler for a sound: {sound:chime/}, {sound:pop on='click', target='#save'/}. The
  // sound is the tag's name, set up as {sound 'chime'} sets it up, by subs/sounds/sound.php -
  // run as a built-in tag's PHP half is run, types/_go/tag.php. A sound is never reached
  // bare: click, error and pop are the words of fields and pages.

  $padSoundKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/sounds/sound';

  return include PAD . 'types/_go/tag.php';

?>
