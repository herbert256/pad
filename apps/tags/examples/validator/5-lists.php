<?php

  $partyRules = padValidateClient ( [
    'guests[]'   => 'required|distinct',
    'toppings[]' => 'required|min_items:2|max_items:3|in:cheese,olives,basil,onion' ] );

?>
