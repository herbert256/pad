<?php

  // The editor's preview: the Markdown posted as 'body', rendered as the note page renders
  // it - asked for by notes.js with &padInclude, the session's token in X-CSRF-Token.

  $previewText = notesText ( 'body' );

?>
