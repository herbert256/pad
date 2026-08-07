<?php

  // local: - returns a file from _data/ as PAD data, the expression form of what types/local.php
  // serves as a tag. Both resolve the name the same way, padDataFileName() searching the
  // application directory chain and then _common and trying the known data extensions, and both
  // load it the same way, padDataFileData() being the function types/local.php inlines.
  //
  // What comes back is an array, which is a value an expression can hold since the three files
  // in eval/go/ deal with arrays. It used to return the literal string 'todo', so local:staff.xml
  // came out as todoxml.
  //
  // Write the name without its extension here. Inside an expression a '.' is the concatenation
  // operator, so local:colors.json is read as local:colors . json and the name arrives cut short;
  // padDataFileName() tries the known extensions anyway, which is why local:colors finds it. The
  // tag form takes either, its name not being tokenised as an expression.

  return padDataFileData ( padDataFileName ( $name ) );

?>