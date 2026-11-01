<?php

  // The rules that look at other fields - required_if, required_unless, required_with,
  // required_without - each with the posts that tell its cases apart: the other field
  // absent, empty, 0 or blanks, a list, a nested name. One table,
  // read twice: forms/client_parity.php adds its rows to the cases the browser's checker
  // must judge as padValidate does (the validate line of ./ci.sh), and the framework cases
  // of rules/ answer padValidate's verdict on the rows of their rule, which pins it. A row
  // is a field, its rules and the post, as client_parity's are; a name in the post is the
  // name the form sends - user[kind] - which the browser has as it is and padFormFind finds
  // as the key it is. The post as PHP files a nested name - [ 'user' => [ 'kind' => ... ] ]
  // - is the framework cases' own, the browser never having it.

  return [

    'required_if' => [
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'ship' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'address' => '   ' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'address' => '0' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'address' => 'Main Street 1' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'collect', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => '', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship',         [ ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => ' ship ', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship',         [ 'delivery' => 'Ship', 'address' => '' ] ],
      [ 'address',  'required_if: delivery , ship , courier', [ 'delivery' => 'courier', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,ship,courier', [ 'delivery' => 'post', 'address' => '' ] ],
      [ 'address',  'required_if:delivery,0',            [ 'delivery' => '0', 'address' => '' ] ],
      [ 'address',  'required_if:kinds[],gift',          [ 'kinds[]' => [ 'book', 'gift' ], 'address' => '' ] ],
      [ 'address',  'required_if:kinds[],gift',          [ 'kinds[]' => [ 'book' ], 'address' => '' ] ],
      [ 'address',  'required_if:kinds[],gift',          [ 'kinds[]' => [ ], 'address' => '' ] ],
      [ 'address',  'required_if:kinds,gift',            [ 'kinds[]' => [ ' gift ' ], 'address' => '' ] ],
      [ 'address',  'required_if:kinds[],gift',          [ 'kinds' => 'gift', 'address' => '' ] ],
      [ 'address',  'required_if:user[kind],company',    [ 'user[kind]' => 'company', 'address' => '' ] ],
      [ 'address',  'required_if:user[kind],company',    [ 'user[kind]' => 'person', 'address' => '' ] ],
      [ 'address',  'required_if:user,company',          [ 'user' => [ 'kind' => 'company' ], 'address' => '' ] ],
      [ 'user[address]', 'required_if:delivery,ship',    [ 'delivery' => 'ship', 'user[address]' => '' ] ],
      [ 'gifts[]',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'gifts[]' => [ ] ] ],
      [ 'gifts[]',  'required_if:delivery,ship',         [ 'delivery' => 'ship', 'gifts[]' => [ 'card' ] ] ],
      [ 'gifts[]',  'required_if:delivery,ship',         [ 'delivery' => 'collect', 'gifts[]' => [ ] ] ],
      [ 'address',  'required_if:delivery,ship|min:5',   [ 'delivery' => 'ship', 'address' => 'Main' ] ],
      [ 'address',  'required_if:delivery,ship|min:5',   [ 'delivery' => 'collect', 'address' => '' ] ]
    ],

    'required_unless' => [
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => 'collect', 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => 'ship', 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => 'ship', 'address' => '0' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => 'ship', 'address' => '  ' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => '', 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => 'Collect' ] ],
      [ 'address',  'required_unless:delivery,collect',  [ 'delivery' => "\tcollect\n", 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect,pickup', [ 'delivery' => 'pickup', 'address' => '' ] ],
      [ 'address',  'required_unless:delivery,collect,pickup', [ 'delivery' => 'ship', 'address' => '' ] ],
      [ 'address',  'required_unless:kinds[],digital',   [ 'kinds[]' => [ 'book', 'digital' ], 'address' => '' ] ],
      [ 'address',  'required_unless:kinds[],digital',   [ 'kinds[]' => [ 'book' ], 'address' => '' ] ],
      [ 'address',  'required_unless:user[kind],person', [ 'user[kind]' => 'person', 'address' => '' ] ],
      [ 'gifts[]',  'required_unless:delivery,collect',  [ 'delivery' => 'ship', 'gifts[]' => [ ] ] ]
    ],

    'required_with' => [
      [ 'code',     'required_with:phone',               [ 'phone' => '0612345678', 'code' => '' ] ],
      [ 'code',     'required_with:phone',               [ 'phone' => '0612345678' ] ],
      [ 'code',     'required_with:phone',               [ 'phone' => '0612345678', 'code' => '31' ] ],
      [ 'code',     'required_with:phone',               [ 'phone' => '0', 'code' => '' ] ],
      [ 'code',     'required_with:phone',               [ 'phone' => '  ', 'code' => '' ] ],
      [ 'code',     'required_with:phone',               [ 'phone' => '', 'code' => '' ] ],
      [ 'code',     'required_with:phone',               [ 'code' => '' ] ],
      [ 'code',     'required_with:phone,fax',           [ 'fax' => '020123', 'code' => '' ] ],
      [ 'code',     'required_with:phone, fax',          [ 'phone' => '', 'fax' => '', 'code' => '' ] ],
      [ 'code',     'required_with:phones[]',            [ 'phones[]' => [ '0612' ], 'code' => '' ] ],
      [ 'code',     'required_with:phones[]',            [ 'phones[]' => [ ], 'code' => '' ] ],
      [ 'code',     'required_with:phones',              [ 'phones[]' => [ '0612' ], 'code' => '' ] ],
      [ 'code',     'required_with:user[phone]',         [ 'user[phone]' => '0612', 'code' => '' ] ],
      [ 'codes[]',  'required_with:phone',               [ 'phone' => '0612', 'codes[]' => [ ] ] ],
      [ 'codes[]',  'required_with:phone',               [ 'phone' => '0612', 'codes[]' => [ '31' ] ] ]
    ],

    'required_without' => [
      [ 'phone',    'required_without:email',            [ 'email' => '', 'phone' => '' ] ],
      [ 'phone',    'required_without:email',            [ ] ],
      [ 'phone',    'required_without:email',            [ 'email' => '   ', 'phone' => '' ] ],
      [ 'phone',    'required_without:email',            [ 'email' => 'ada@example.com', 'phone' => '' ] ],
      [ 'phone',    'required_without:email',            [ 'email' => '0', 'phone' => '' ] ],
      [ 'phone',    'required_without:email',            [ 'email' => '', 'phone' => '0612' ] ],
      [ 'phone',    'required_without:email',            [ 'email' => '', 'phone' => '0' ] ],
      [ 'phone',    'required_without:email,fax',        [ 'email' => 'ada@example.com', 'phone' => '' ] ],
      [ 'phone',    'required_without:email,fax',        [ 'email' => 'ada@example.com', 'fax' => '020', 'phone' => '' ] ],
      [ 'phone',    'required_without:emails[]',         [ 'emails[]' => [ ], 'phone' => '' ] ],
      [ 'phone',    'required_without:emails[]',         [ 'emails[]' => [ 'ada@example.com' ], 'phone' => '' ] ],
      [ 'phone',    'required_without:user[email]',      [ 'user[email]' => '', 'phone' => '' ] ],
      [ 'phone',    'required_without:user[email]',      [ 'user[email]' => 'ada@example.com', 'phone' => '' ] ],
      [ 'phones[]', 'required_without:email',            [ 'email' => '', 'phones[]' => [ ] ] ]
    ]

  ];

?>
