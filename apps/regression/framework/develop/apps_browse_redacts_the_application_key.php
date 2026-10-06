<?php

  // The apps browser blanks the secrets in the sources it shows - the passwords, tokens and
  // API keys - and the application key is one: $padAppKey signs every link padSignedUrl
  // makes and opens every value padEncrypt sealed. A fixture of the Errors suite sets one.

  $curl   = padCurl ( $padHost . 'apps/?browse&app=regression/errors&file=helpers/encryption_names_an_app_key_of_the_wrong_length.php&padInclude' );
  $answer = $curl ['result'] . ': ' . ( str_contains ( $curl ['data'], 'too short' ) ? 'the key shown' : 'the key redacted' );

?>
