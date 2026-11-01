<?php

  // The notes of a noise voice may be written x - a hit of white noise each, - a rest: 'x x - x'
  // settles to hits at 0, one step and three steps, each of 0 Hz, which both renderers play as
  // white noise with the voice's envelope from every hit - silent between the hits and in the
  // rest. The clap is three of them, a hundredth of a second apart.

  include_once PAD . 'subs/sounds/_lib/sound.php';

  $soundRun   = padSoundSettle ( 'test', [ 'voices' => [ [ 'wave' => 'noise', 'notes' => 'x x - x', 'step' => 0.1, 'length' => 0.05 ] ] ] );
  $soundPcm   = padSoundRender ( $soundRun );
  $soundHits  = fn ( $voice ) => implode ( ' ', array_map ( fn ( $n ) => $n [0] . '@' . $n [1], $voice ['notes'] ) );
  $soundQuiet = fn ( $from, $to ) => max ( array_map ( 'abs', array_slice ( $soundPcm, (int) ( $from * 22050 ), (int) ( ( $to - $from ) * 22050 ) ) ) );

  $soundNoise = 'run ' . $soundHits ( $soundRun ['voices'] [0] ) . ' length ' . $soundRun ['length']
              . ', loudest between the hits ' . $soundQuiet ( 0.051, 0.099 ) . ', in the rest ' . $soundQuiet ( 0.151, 0.299 )
              . ', in the last hit ' . $soundQuiet ( 0.3, 0.35 )
              . ', clap ' . $soundHits ( padSoundRecipe ( 'clap' ) ['voices'] [0] );

?>
