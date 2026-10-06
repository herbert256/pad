<?php

  // The files of an application, and what git says about them.

  if ( ! empty ( $body ['engine'] ) )
    return [ 'files' => editEngineTree (), 'git' => empty ( $body ['git'] ) ? NULL : editGitEngine () ];

  $app = editApp ( editArg ( $body, 'app' ) );

  return [ 'files'  => editTree ( $app ),
           'git'    => empty ( $body ['git'] ) ? NULL : editGitStatus ( $app ),
           'branch' => empty ( $body ['git'] ) ? '' : editGitBranch () ];

?>
