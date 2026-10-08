<?php

  padCurlFake ( [ 'https://a.example/*' => 'faked' ] );

  padCurl ( 'https://b.example/x' );

?>
