<?php

  // The {pad:ai} pair renders its content first and sends what it rendered - a field's value as
  // the text it holds, & and < as themselves, not as their HTML escapes. The answer is a value:
  // a model that answers markup or a PAD tag gets it printed as text. The key is read with
  // padEnv - here from $_ENV, as the server's environment would give it.

  $_ENV ['ANTHROPIC_API_KEY'] = 'sk-test';

  $body = 'Tom & Jerry <the cat> chase each other';

  padCurlFake ( [ 'https://api.anthropic.com/v1/messages' => [ 'data' => [
    'stop_reason' => 'end_turn', 'content' => [ [ 'type' => 'text', 'text' => 'A cat <b>chases</b> a mouse {php:getcwd} & loses.' ] ] ] ] ] );

  function aiTagSent () {
    $body = json_decode ( padCurlRecorded () [0] ['post'], TRUE );
    return $body ['system'] . ' | ' . $body ['messages'] [0] ['content'] . ' | ' . $body ['max_tokens'] . ' | ' . $body ['model'];
  }

?>
