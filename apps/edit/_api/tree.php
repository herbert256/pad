<?php

  // The files of an application, and what git says about them.

  $app = editApp ( editArg ( $body, 'app' ) );

  return [ 'files'  => editTree ( $app ),
           'git'    => empty ( $body ['git'] ) ? NULL : editGitStatus ( $app ),
           'branch' => empty ( $body ['git'] ) ? '' : editGitBranch () ];

?>
