<?php

  // Shared body for a tag whose own name is a data format: the tag name is taken as the
  // type and the parameter as a file's base name, so {xml 'menu'} would make menu.xml
  // this level's data, with $padForceTagName making the level answer to that name
  // (level/name.php).
  //
  // Nothing in the engine includes this file, and padData() expects the data itself
  // rather than a file name, so as written the name is handed to data/<type>.php as if it
  // were the content. The working equivalents are the local: type prefix and {data}.

  $padMakeType = $padTag [$pad];
  $padMakeFile = $padParm;

  $padData [$pad] = padData ("$padMakeFile.$padMakeType", $padMakeType, $padMakeFile);

  $padForceTagName = $padMakeFile;

  return TRUE;

?>