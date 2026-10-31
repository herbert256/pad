<?php

  // The rich form fields are the files of pad/inputs/types/ - padInputTypes reads them there,
  // as the input: type and the bare names do - and each has its script beside it in
  // pad/inputs/assets/ unless its style alone draws it.

  $inputFiles = array_map ( fn ( $file ) => basename ( $file, '.php' ), glob ( PAD . 'inputs/types/*.php' ) );

  $inputList = ( padInputTypes () === $inputFiles ) ? 'the files of inputs/types' : 'another list';

?>
