<?php

  // The pages the build writes stand in the sequence application - apps/sequence/basic/,
  // keepRemoveFlag/ and play/ - where this looked for them in develop's own sequence/
  // directory, which has none. And the build writes them, the engine's flags/ markers and
  // a type's generated.php natively, as the trimmer reads and writes (_lib/trim.php):
  // padFilePut writes under DATA/ only, and every write ended the build on "Invalid file
  // (contains '//')" - after flags.php had emptied the first type's flags/ directory.

  foreach ( glob ( APPS . 'sequence/basic/*'            ) as $file ) unlink($file);
  foreach ( glob ( APPS . 'sequence/keepRemoveFlag/*'   ) as $file ) unlink($file);

  foreach ( glob ( APPS . 'sequence/play/single/*' ) as $file ) unlink($file);
  foreach ( glob ( APPS . 'sequence/play/double/*' ) as $file ) unlink($file);

?>
