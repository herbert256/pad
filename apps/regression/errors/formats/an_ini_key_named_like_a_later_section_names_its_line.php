<?php

  // A key of the whole and a section of the same name: the section's keys have no record to
  // go in - the text's error at the section's line, where the key's text was taken for the
  // record and the request ended on "Cannot access offset of type string on string".

  $ini = padFormatRead ( 'ini', "a = 1\n[a]\nb = 2" );

?>
