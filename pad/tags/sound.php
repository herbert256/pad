<?php

  // {sound 'chime', button='Play'/} - a short sound made from a recipe and played by the
  // browser, of the kind its parameter names: a kind is a file of subs/sounds/types/, played
  // by the sounds subsystem in subs/sounds/ - the same sound as the sound: type writes it,
  // {sound:chime/}.

  $padSoundKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'subs/sounds/sound.php';

?>
