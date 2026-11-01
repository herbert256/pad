<?php

  // x is a hit of white noise in the notes of a noise voice; in the notes of a sine it is no
  // note, and the recipe says so.

  include_once PAD . 'subs/sounds/_lib/sound.php';

  padSoundSettle ( 'test', [ 'voices' => [ [ 'wave' => 'sine', 'notes' => 'C5 x E5' ] ] ] );

?>
