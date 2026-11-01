<?php

  // bullscows - bulls and cows: find the secret number of four different digits. Each guess
  // is answered with its bulls - right digits in the right place - and its cows - right
  // digits in the wrong place. digits= how long the number is, 4 unless given (3 to 6);
  // tries= how many guesses there are, 10 unless given (4 to 20).

  return padGame ( 'bullscows', 'Bulls and cows', [
    'digits' => padGameInt ( 'digits', 4, 3, 6 ),
    'tries'  => padGameInt ( 'tries', 10, 4, 20 )
  ] );

?>
