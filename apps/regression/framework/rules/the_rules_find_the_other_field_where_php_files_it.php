<?php

  // The required_if family finds the other field where PHP files the post - user[kind] in
  // [ 'user' => [ 'kind' => ... ] ], kinds[] as the list kinds, first.name as first_name - as
  // padFormFind finds the field itself, and in data that is an ArrayObject, a list that is
  // one too. user, the parent of user[kind], is no field: an array with keys of its own is
  // empty to them, as the browser has no field of that name either.

  $ruleRows = [
    [ 'address',       'required_if:user[kind],company',     [ 'user' => [ 'kind' => 'company' ], 'address' => '' ] ],
    [ 'address',       'required_if:user[kind],company',     [ 'user' => [ 'kind' => 'person' ], 'address' => '' ] ],
    [ 'user[address]', 'required_unless:user[kind],person',  [ 'user' => [ 'kind' => 'company', 'address' => '' ] ] ],
    [ 'user[address]', 'required_with:user[phone]',          [ 'user' => [ 'phone' => '0612' ] ] ],
    [ 'user[address]', 'required_without:user[phone]',       [ 'user' => [ 'phone' => ' ' ] ] ],
    [ 'address',       'required_if:kinds,gift',             [ 'kinds' => [ 'book', 'gift' ], 'address' => '' ] ],
    [ 'address',       'required_if:kinds[],gift',           [ 'kinds' => [ 'gift' ], 'address' => '' ] ],
    [ 'address',       'required_without:first.name',        [ 'first_name' => 'Ada', 'address' => '' ] ],
    [ 'address',       'required_with:user',                 [ 'user' => [ 'phone' => '0612' ], 'address' => '' ] ],
    [ 'address',       'required_if:delivery,ship',          new ArrayObject ( [ 'delivery' => 'ship', 'address' => '' ] ) ],
    [ 'address',       'required_if:kinds,gift',             [ 'kinds' => new ArrayObject ( [ 'gift' ] ), 'address' => '' ] ]
  ];

  $ruleVerdicts = [];

  foreach ( $ruleRows as $ruleAt => [ $ruleField, $ruleList, $ruleData ] )
    $ruleVerdicts [] = ( $ruleAt + 1 ) . ". $ruleField $ruleList : " . ( padValidate ( [ $ruleField => $ruleList ], $ruleData ) [$ruleField] ?? 'passes' );

  $ruleVerdicts = implode ( ' ; ', $ruleVerdicts );

?>
