<?php

  // Passwords with a / or a second @ in them, unencoded: the cut ended early and the rest
  // of the password was on the page.

  $remotes = [];

  foreach ( [ 'https://ann:secret@example.com/r.git', 'https://ann:se/cr/et@example.com/r.git',
              'https://ann:p@ss@example.com/r.git', 'https://example.com/r.git', 'git@example.com:r.git' ] as $remote )
    $remotes [] = adminRemoteShown ( $remote );

  $remotes = implode ( ' ', $remotes );

?>
