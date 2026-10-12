<?php

  // Under a policy whose style-src asks for a nonce, the <style> the engine writes for a
  // chart, a table, a widget carries it, where they were refused and every chart drew its
  // bars black.

  $padCsp = "default-src 'self'; style-src 'nonce'";

?>
