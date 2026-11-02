<?php

  // Anthropic is not asked here: a fake answers in the shape of its Messages API, as a test
  // answers it. In an application the key stands in .env as ANTHROPIC_API_KEY; here it is
  // put where padEnv finds it first.

  $_ENV ['ANTHROPIC_API_KEY'] = 'sk-example';

  padCurlFake ( [ 'https://api.anthropic.com/v1/messages' => [ 'data' => [
    'stop_reason' => 'end_turn',
    'content'     => [ [ 'type' => 'text', 'text' => 'Two friends trade pots of <b>jam</b> & {stories} across one long winter.' ] ] ] ] ] );

  $body = 'All winter long Ann and Bo swapped jars of jam over the fence, and with every jar a story.';

?>
