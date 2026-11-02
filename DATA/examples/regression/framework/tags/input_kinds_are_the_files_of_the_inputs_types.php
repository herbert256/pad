<?php

  // The rich form fields are the files of pad/subs/inputs/types/ - padInputTypes reads them there,
  // as the input: type and the bare names do - and each has its script beside it in
  // www/assets/inputs/ unless its style alone draws it.

  $inputFiles = array_map ( fn ( $file ) => basename ( $file, '.php' ), glob ( PAD . 'subs/inputs/types/*.php' ) );

  $inputList = ( padInputTypes () === $inputFiles ) ? 'the files of subs/inputs/types' : 'another list';

?>
