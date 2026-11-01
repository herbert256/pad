<?php

  padCurlFake ( [ 'https://api.anthropic.com/*' => [ 'status' => 529, 'data' => [ 'type' => 'error', 'error' => [ 'type' => 'overloaded_error', 'message' => 'Overloaded' ] ] ] ] );

  padAi ( 'Hello?', [ 'provider' => 'anthropic', 'key' => 'sk-test' ] );

?>
