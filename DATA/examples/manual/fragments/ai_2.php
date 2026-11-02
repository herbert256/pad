<?php

  // Ollama runs a model on this machine and needs no key - here a fake answers in its shape,
  // and padCurlRecorded shows what was sent.

  padCurlFake ( [ 'http://127.0.0.1:11434/api/chat' => [ 'data' => [ 'message' => [ 'role' => 'assistant', 'content' => 'Loire, Seine, Rhone.' ] ] ] ] );

  $rivers = padAi ( 'Name three rivers of France.', [ 'provider' => 'ollama', 'system' => 'Answer in one line.', 'tokens' => 40 ] );

  $sent     = json_decode ( padCurlRecorded () [0] ['post'], TRUE );
  $messages = $sent ['messages'];

  padCurlFakeStop ();

?>
