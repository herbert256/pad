<?php

  // Under a policy whose style-src asks for a nonce, the <style> of {tabs}, {accordion},
  // {modal} and {copy} carries it as {img} and {video} do: without their CSS a tab set showed
  // every panel and bare radio buttons, a modal its button and its no-script link both.

  $padCsp = "default-src 'self'; script-src 'nonce'; style-src 'nonce'";

?>
