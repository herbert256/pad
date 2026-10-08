<?php

  // One list of rules for both sides: padValidate checks the post with it, and the
  // component that draws the form gets it as padValidateClient exports it.

  $rules = [ 'email' => 'required|email', 'qty' => 'integer|min:1|max:10' ];

  $client = json_encode ( padValidateClient ( $rules ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
