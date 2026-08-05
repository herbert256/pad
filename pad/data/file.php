<?php

  // Treats $data as the name of a file in _data/ and returns its contents as a PAD data
  // array. padDataFileName searches _data/ from the current directory up to the app root
  // and then _common, trying the bare name plus the .xml/.json/.yaml/.csv/.php/.curl/.sql
  // extensions; padDataFileData loads it through types/_go/local.php, which re-enters
  // padData() with the file extension as the type. Included by padData() as
  // data/<type>.php; padContentType picks 'file' when such a file exists.

  $check = padDataFileName ( $data );

  return padDataFileData ( $check );

?>