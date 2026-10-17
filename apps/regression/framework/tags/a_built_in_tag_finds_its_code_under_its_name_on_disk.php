<?php

  // The code of a built-in tag, tags/_lib/<tag>.php, is included under the file's own name,
  // whatever the case the tag was written in: on a file system that ignores case {Avatar} is
  // the avatar tag, and include_once of tags/_lib/Avatar.php after {avatar}'s
  // tags/_lib/avatar.php ended the page on "Cannot redeclare function padAvatar()".

  $libs = implode ( ' ', array_map ( fn ( $tag ) => basename ( (string) padTagLib ( $tag ) ) ?: '-', [ 'avatar', 'Avatar', 'TABS', 'if' ] ) );

?>
