<?php

  // wordsearch - the words hide in a square of letters, across, down and diagonal, forwards
  // and backwards: click the first letter of a word and then its last to find it. The words
  // come between the tags, one a line, as a list, or by data=; without them a list built in.
  // size= the square, as large as the longest word needs unless given (6 to 20).

  $padWordsList = padGameWords ( padGameList (), 18 );
  $padWordsLong = max ( array_map ( 'strlen', $padWordsList ?: [ 'x' ] ) );

  return padGame ( 'wordsearch', 'Word search', [
    'words' => array_slice ( $padWordsList, 0, 20 ),
    'size'  => padGameInt ( 'size', max ( 10, $padWordsLong + 2 ), max ( 6, $padWordsLong ), 20 )
  ] );

?>
