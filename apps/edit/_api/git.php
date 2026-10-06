<?php

  // A file as it is in git's HEAD - NULL when HEAD does not have it.

  [ $app, $root, $rel ] = editTarget ( $body );

  if ( ! editGit () )
    editFail ( 'there is no git here' );

  return [ 'text' => editGitHead ( $app, $root, $rel ) ];

?>
