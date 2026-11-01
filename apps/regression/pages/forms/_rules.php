<?php

  // The rules that look at other fields - required_if, required_unless, required_with,
  // required_without, before, after - and not_in, each with the posts that tell its cases
  // apart: the other field absent, empty, 0 or blanks, a list, a nested name, the two orders
  // of two dates, the same date, a date that is none, a date written in the rule. One table,
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
    ],

    'before' => [
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '2026-01-02' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-02', 'end' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2025-12-31', 'end' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-02-30', 'end' => '2026-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-13-01', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '0000-01-01', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => 'tomorrow', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-1-5', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '01-05-2026', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-05 09:00', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-05T24:00', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-05T09:00Z', 'end' => '2027-12-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2028-02-29', 'end' => '2028-03-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2027-02-29', 'end' => '2028-03-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => ' 2026-01-01 ', 'end' => ' 2026-01-02 ' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => 'someday' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '2026-02-30' ] ],
      [ 'start',    'before:end',                        [ 'start' => 'someday', 'end' => '' ] ],
      [ 'start',    'before:end',                        [ 'start' => '', 'end' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'end' => '2026-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T09:00', 'end' => '2026-01-01T10:30' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T10:30', 'end' => '2026-01-01T09:00' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T09:00:30', 'end' => '2026-01-01T09:00' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T09:00', 'end' => '2026-01-01T09:00:00' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '2026-01-01T00:00' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01', 'end' => '2026-01-01T00:01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T23:59:59', 'end' => '2026-01-02' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-01-01T09:60', 'end' => '2026-01-02' ] ],
      [ 'start',    'before:end[]',                      [ 'start' => '2026-01-01', 'end[]' => [ '2025-01-01' ] ] ],
      [ 'start',    'before:trip[end]',                  [ 'start' => '2026-02-01', 'trip[end]' => '2026-01-01' ] ],
      [ 'start',    'before:trip[end]',                  [ 'start' => '2025-02-01', 'trip[end]' => '2026-01-01' ] ],
      [ 'start',    'before:2027-01-01',                 [ 'start' => '2026-12-31' ] ],
      [ 'start',    'before:2027-01-01',                 [ 'start' => '2027-01-01' ] ],
      [ 'start',    'before:2027-01-01',                 [ 'start' => '2027-01-02' ] ],
      [ 'start',    'before:2027-01-01T12:00',           [ 'start' => '2027-01-01T11:59' ] ],
      [ 'start',    'before:2027-01-01T12:00',           [ 'start' => '2027-01-01' ] ],
      [ 'start',    'before:2027-01-01',                 [ 'start' => '2026-12-31', '2027-01-01' => '2000-01-01' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-11-01T14:30:15.999', 'end' => '2026-11-01T14:30:16' ] ],
      [ 'start',    'before:end',                        [ 'start' => '2026-11-01T14:30:15.5', 'end' => '2026-11-01T14:30:15.500' ] ],
      [ 'start',    'before:2026-11-01T14:30:15.5',      [ 'start' => '2026-11-01T14:30:15.25' ] ],
      [ 'start',    'required|before:end',               [ 'start' => '', 'end' => '2026-01-01' ] ],
      [ 'starts[]', 'before:end',                        [ 'starts[]' => [ '2026-01-01' ], 'end' => '2027-01-01' ] ]
    ],

    'after' => [
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-01-02' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-02', 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-02-30' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => 'next week' ] ],
      [ 'end',      'after:start',                       [ 'start' => '', 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-02-30', 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01T09:00', 'end' => '2026-01-01T09:01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01T09:00', 'end' => '2026-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-01-01T00:00:01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-01-01T00:00' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-01-01', 'end' => '2026-01-01t10:00' ] ],
      [ 'end',      'after:start',                       [ 'start' => '1999-12-31', 'end' => '2000-01-01' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01T09:00', 'end' => '2026-11-01T14:30:15.25' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01T14:30:15.5', 'end' => '2026-11-01T14:30:15.25' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01T14:30:15', 'end' => '2026-11-01T14:30:15.001' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01T14:30:15.1234', 'end' => '2026-11-01T14:30:16' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01', 'end' => '2026-11-01T14:30:15.1234' ] ],
      [ 'end',      'after:start',                       [ 'start' => '2026-11-01', 'end' => '2026-11-01T14:30.5' ] ],
      [ 'end',      'after:trip[start]',                 [ 'trip[start]' => '2026-06-01', 'end' => '2026-05-01' ] ],
      [ 'end',      'after:2026-01-01',                  [ 'end' => '2026-01-02' ] ],
      [ 'end',      'after:2026-01-01',                  [ 'end' => '2026-01-01' ] ],
      [ 'end',      'after:2026-01-01',                  [ 'end' => '2025-12-31T23:59' ] ],
      [ 'end',      'after: 2026-01-01T08:00 ',          [ 'end' => '2026-01-01T08:00:01' ] ],
      [ 'end',      'after:2026-01-01',                  [ 'end' => '2025-12-31', '2026-01-01' => '1999-01-01' ] ],
      [ 'end',      'after:start_date',                  [ 'start_date' => '2026-03-01', 'end' => '2026-02-01' ] ]
    ],

    'not_in' => [
      [ 'user',     'not_in:admin,root',                 [ 'user' => 'admin' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => 'root' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => ' root ' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => 'Admin' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => 'ada' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => 'administrator' ] ],
      [ 'user',     'not_in:admin,root',                 [ 'user' => '' ] ],
      [ 'user',     'not_in:admin,root',                 [ ] ],
      [ 'user',     'not_in: admin , root ',             [ 'user' => 'root' ] ],
      [ 'code',     'not_in:0,1',                        [ 'code' => '0' ] ],
      [ 'code',     'not_in:0,1',                        [ 'code' => '00' ] ],
      [ 'user',     'required|not_in:admin',             [ 'user' => '' ] ],
      [ 'roles[]',  'not_in:admin,root',                 [ 'roles[]' => [ 'editor', 'viewer' ] ] ],
      [ 'roles[]',  'not_in:admin,root',                 [ 'roles[]' => [ 'editor', 'admin' ] ] ],
      [ 'roles[]',  'not_in:admin,root',                 [ 'roles[]' => [ 'editor', ' root ' ] ] ],
      [ 'roles[]',  'not_in:admin,root',                 [ 'roles[]' => [ ] ] ],
      [ 'role',     'not_in:admin,root',                 [ 'role' => [ 'editor' ] ] ]
    ],

    // The rules of the second round (types: rules), each with the posts that tell its cases
    // apart, replayed alike by the framework case of its rule and by client_parity.

    'different' => [
      [ 'password', 'different:current',                 [ 'current' => 'secret', 'password' => 'secret' ] ],
      [ 'password', 'different:current',                 [ 'current' => 'secret', 'password' => 'secret2' ] ],
      [ 'password', 'different:current',                 [ 'current' => ' secret ', 'password' => 'secret' ] ],
      [ 'password', 'different:current',                 [ 'current' => 'Secret', 'password' => 'secret' ] ],
      [ 'password', 'different:current',                 [ 'current' => '0', 'password' => '0' ] ],
      [ 'password', 'different:current',                 [ 'current' => '', 'password' => 'secret' ] ],
      [ 'password', 'different:current',                 [ 'password' => 'secret' ] ],
      [ 'password', 'different:current',                 [ 'current' => 'secret', 'password' => '' ] ],
      [ 'password', 'different:current',                 [ 'current' => 'secret' ] ],
      [ 'password', 'required|different:current',        [ 'current' => 'secret', 'password' => '' ] ],
      [ 'password', 'different: current ',               [ 'current' => 'secret', 'password' => 'secret' ] ],
      [ 'password', 'different:user[pass]',              [ 'user[pass]' => 'secret', 'password' => 'secret' ] ],
      [ 'password', 'different:user[pass]',              [ 'user[pass]' => 'other', 'password' => 'secret' ] ],
      [ 'password', 'different:old[]',                   [ 'old[]' => [ 'secret' ], 'password' => 'secret' ] ],
      [ 'new_password', 'different:current_password',    [ 'current_password' => 'pw', 'new_password' => 'pw' ] ],
      [ 'new_password', 'different:current_password|min:3', [ 'current_password' => 'pw', 'new_password' => 'px' ] ],
      [ 'passwords[]', 'different:current',              [ 'current' => 'a', 'passwords[]' => [ 'b' ] ] ],
      [ 'password', 'different:current',                 [ 'current' => 'a', 'password' => [ 'b' ] ] ]
    ]

  ];

?>
