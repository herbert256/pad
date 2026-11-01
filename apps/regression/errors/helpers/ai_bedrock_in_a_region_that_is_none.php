<?php

  // The region of Amazon Bedrock is a part of its address: AWS_REGION of another form - a city,
  // a space in it - is refused before anything is asked.

  $_ENV ['AWS_REGION'] = 'Frankfurt am Main';

  padAi ( 'Hello?', [ 'provider' => 'bedrock', 'key' => 'bedrock-test' ] );

?>
