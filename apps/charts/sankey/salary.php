<?php

  // A net salary of 3,200 a month: the posts, and what they are made of.

  $spending = [
    'Housing'   => [ 'Rent' => 1150, 'Insurance' => 120 ],
    'Living'    => [ 'Groceries' => 450, 'Energy' => 160, 'Phone' => 40 ],
    'Transport' => [ 'Car' => 260, 'Train' => 90 ],
    'Saving'    => [ 'Pension' => 250, 'Holiday' => 180 ],
    'Fun'       => [ 'Dining out' => 220, 'Sport' => 80, 'Streaming' => 30 ]
  ];

  $salary = [];

  foreach ( $spending as $post => $parts )
    foreach ( $parts as $part => $amount ) {
      $salary [] = [ 'from' => 'Salary', 'to' => $post, 'amount' => $amount ];
      $salary [] = [ 'from' => $post,    'to' => $part, 'amount' => $amount ];
    }

?>
