<?php

  // The rules of the second round, posted for real: the first post breaks them all - the new
  // password the one in use, 20 bottles where they come by six, a highest price that is the
  // lowest, a stay that leaves before it arrives, one topping, a note of six words - and comes
  // back refilled with each message; the second keeps them - the stay leaving on the day it
  // arrives, two toppings, 0.3e2 bottles a number of whole crates - and is stored; the third
  // ticks four toppings and writes six words parted by a line break and a no-break space,
  // the prices and the dates left empty passing. The session's token is left out of what is
  // compared.

  $partyResult = '';

  foreach ( [ 'current=secret&password=secret&bottles=20&min_price=25&max_price=25&arrive=2026-07-10&leave=2026-07-09'
              . '&toppings[]=cheese&note=one+two+three+four+five+six',
              'current=secret&password=secret2&bottles=0.3e2&min_price=25&max_price=25.5&arrive=2026-07-10&leave=2026-07-10'
              . '&toppings[]=cheese&toppings[]=olives&note=one+two',
              'current=&password=secret&bottles=&min_price=&max_price=1&arrive=&leave=2026-07-10'
              . '&toppings[]=cheese&toppings[]=olives&toppings[]=basil&toppings[]=onion&note=one%0Atwo%C2%A0three+four+five+six' ] as $partyPost ) {

    $partyCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_that_compare_and_count&padInclude',
                             'post' => 'padForm=party&' . $partyPost ] );

    $partyResult .= $partyCurl ['result'] . "\n"
                  . preg_replace ( '/ value="[0-9a-f]{64}"/', '', trim ( $partyCurl ['data'] ) ) . "\n\n";

  }

?>
