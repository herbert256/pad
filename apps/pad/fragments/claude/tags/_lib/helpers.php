<?php

  function formatPrice($amount) {
    return '$' . number_format($amount, 2);
  }

  function greetUser($name) {
    return "Hello, $name!";
  }

?>