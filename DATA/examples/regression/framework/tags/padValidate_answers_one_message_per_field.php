<?php

  $validated = padValidate (
    [ 'name'    => 'required',
      'email'   => 'required|email',
      'site'    => 'url',
      'age'     => 'integer|min:18',
      'code'    => 'max:3',
      'size'    => 'in:S,M,L',
      'zip'     => [ 'regex:/^\d{4}[A-Z]{2}$/' ],
      'again'   => 'same:password',
      'terms'   => 'accepted',
      'born'    => 'date',
      'note'    => 'max:5',
      'nick'    => 'required|min:3' ],
    [ 'name' => '', 'email' => 'a@b', 'site' => 'ftp://x', 'age' => '17', 'code' => 'abcd',
      'size' => 'XL', 'zip' => '1234ab', 'password' => 'p1', 'again' => 'p2', 'born' => '2026-02-30',
      'note' => '', 'nick' => 'ab' ],
    [ 'nick.min' => 'Pick a longer nickname' ] );

  $result = '';

  foreach ( $validated as $field => $message )
    $result .= "$field: $message. ";

?>
