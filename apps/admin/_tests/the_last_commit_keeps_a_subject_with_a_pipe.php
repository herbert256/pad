<?php

  // A commit subject with a PAD pipe in it - {$x | upper} - was cut at the pipe and its rest
  // taken for the author; the time was the author's name, so no "ago".

  [ $gitHash, $gitSubject, $gitAuthor, $gitTime ] = adminGitLast ( [ 0, "abc1234\x1f1760000000\x1fherbert\x1fUse {\$x | upper} in a | pipe\n" ] );

  $gitReal = adminGitLast () [0] === trim ( adminGit ( [ 'rev-parse', '--short', 'HEAD' ] ) [1] ) ? 'the checkout' : 'something else';

?>
