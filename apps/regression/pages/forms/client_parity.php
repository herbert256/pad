<?php

  // The cases the browser's checker (pad/lib/validate.js) must judge as padValidate does:
  // each a field, its rules and the posted values, with the message padValidate gives and
  // the rules as padValidateClient exports them. This page answers them as JSON - its data,
  // through $padExpose - and its answer pins the server's side; _parity.js, the parity line
  // of ./ci.sh, runs the checker over the same cases and compares.

  $padCases = [
    [ 'name',     'required',                 [ 'name' => '' ] ],
    [ 'name',     'required',                 [ 'name' => '   ' ] ],
    [ 'name',     'required',                 [ 'name' => 'Ada' ] ],
    [ 'terms',    'accepted',                 [ 'terms' => 'yes' ] ],
    [ 'terms',    'accepted',                 [ 'terms' => 'no' ] ],
    [ 'terms',    'accepted',                 [ ] ],
    [ 'email',    'required|email',           [ 'email' => 'ada@example.com' ] ],
    [ 'email',    'required|email',           [ 'email' => 'user+tag@mail.example.co.uk' ] ],
    [ 'email',    'required|email',           [ 'email' => 'ada@example' ] ],
    [ 'email',    'required|email',           [ 'email' => 'not an address' ] ],
    [ 'email',    'required|email',           [ 'email' => 'a..b@example.com' ] ],
    [ 'email',    'required|email',           [ 'email' => '.ada@example.com' ] ],
    [ 'email',    'required|email',           [ 'email' => 'ada@-example.com' ] ],
    [ 'email',    'email',                    [ 'email' => '' ] ],
    [ 'site',     'url',                      [ 'site' => 'https://example.com/a?b=c' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://localhost' ] ],
    [ 'site',     'url',                      [ 'site' => 'ftp://example.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'example.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'https://' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '12' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '-1.5' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '1e3' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '.5' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '5.' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => 'abc' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '0x1A' ] ],
    [ 'amount',   'numeric',                  [ 'amount' => '1 2' ] ],
    [ 'count',    'integer',                  [ 'count' => '42' ] ],
    [ 'count',    'integer',                  [ 'count' => '-7' ] ],
    [ 'count',    'integer',                  [ 'count' => '+3' ] ],
    [ 'count',    'integer',                  [ 'count' => '007' ] ],
    [ 'count',    'integer',                  [ 'count' => '1.0' ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => 'ab' ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => 'abc' ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => 'abcdef' ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => 'éèê' ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => '😀😀' ] ],
    [ 'age',      'numeric|min:18|max:120',   [ 'age' => '17' ] ],
    [ 'age',      'numeric|min:18|max:120',   [ 'age' => '18' ] ],
    [ 'age',      'numeric|min:18|max:120',   [ 'age' => '121.5' ] ],
    [ 'age',      'integer|min:18',           [ 'age' => 'twenty' ] ],
    [ 'color',    'in:red, green',            [ 'color' => 'green' ] ],
    [ 'color',    'in:red, green',            [ 'color' => 'blue' ] ],
    [ 'color',    'in:red, green',            [ 'color' => 'Red' ] ],
    [ 'code',     'regex:/^[a-z]+$/i',        [ 'code' => 'Hello' ] ],
    [ 'code',     'regex:/^[a-z]+$/i',        [ 'code' => 'Hello1' ] ],
    [ 'year',     'regex:#^\d{4}$#',          [ 'year' => '2026' ] ],
    [ 'year',     'regex:#^\d{4}$#',          [ 'year' => '26' ] ],
    [ 'name',     'regex:/^[\w\s-]+$/u',       [ 'name' => 'José Müller' ] ],
    [ 'name',     'regex:/^.{5}$/',           [ 'name' => 'José' ] ],
    [ 'zip',      'regex:/\A\d{4}\z/',        [ 'zip' => '1234' ] ],
    [ 'slug',     'regex:/^[[:alnum:]-]+$/',  [ 'slug' => 'abc-1' ] ],
    [ 'code',     'regex:/^[^]]+$/',          [ 'code' => 'abc' ] ],
    [ 'code',     'regex:/^[]a-z]+$/',        [ 'code' => 'a]b' ] ],
    [ 'notes',    'min:4',                    [ 'notes' => "a\r\nb" ] ],
    [ 'notes',    'max:3',                    [ 'notes' => "a\r\nb" ] ],
    [ 'confirm',  'same:password',            [ 'password' => 'secret', 'confirm' => 'secret' ] ],
    [ 'confirm',  'same:password',            [ 'password' => 'secret', 'confirm' => 'secrets' ] ],
    [ 'name',     'required',                 [ 'name' => "\u{00A0}" ] ],
    [ 'nick',     'min:3|max:5',              [ 'nick' => "\u{00A0}ab\u{00A0}" ] ],
    [ 'email',    'required|email',           [ 'email' => "ada@example.com\u{00A0}" ] ],
    [ 'born',     'date',                     [ 'born' => '2026-10-08' ] ],
    [ 'born',     'date',                     [ 'born' => '2026-02-30' ] ],
    [ 'born',     'date',                     [ 'born' => '2026-13-01' ] ],
    [ 'born',     'date',                     [ 'born' => '31-12-2026' ] ],
    [ 'born',     'date',                     [ 'born' => '12/31/2026' ] ],
    [ 'born',     'date',                     [ 'born' => 'October 8, 2026' ] ],
    [ 'born',     'date',                     [ 'born' => 'someday' ] ],
    [ 'born',     'date',                     [ 'born' => '0099-12-31' ] ],
    [ 'born',     'date',                     [ 'born' => '2026-W42' ] ],
    [ 'born',     'date',                     [ 'born' => '20260105' ] ],
    [ 'born',     'date',                     [ 'born' => '2026-01-05 24:00' ] ],
    [ 'born',     'date',                     [ 'born' => '2026-01-05 25:00' ] ],
    [ 'tags[]',   'required|in:a,b',          [ 'tags[]' => [] ] ],
    [ 'tags[]',   'required|in:a,b',          [ 'tags[]' => [ 'a', 'b' ] ] ],
    [ 'tags[]',   'required|in:a,b',          [ 'tags[]' => [ 'a', 'c' ] ] ],
    [ 'color',    'required',                 [ 'color' => [ 'red', 'green' ] ] ],
    [ 'email',    'required|email',           [ 'email' => '' ], [ 'email.required' => 'Fill in :label, please' ] ],
    [ 'nick',     'max:5',                    [ 'nick' => 'toolong' ], [ 'nick' => ':label: :n at most' ] ],
    [ 'first_name', 'required',               [ ] ],
    [ 'email',    'email',                    [ 'email' => 'user@[127.0.0.1]' ] ],
    [ 'email',    'email',                    [ 'email' => 'a@b.123' ] ],
    [ 'email',    'email',                    [ 'email' => 'a@1.2.3.4' ] ],
    [ 'site',     'url',                      [ 'site' => 'https://999.2.3.4' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://exämple.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'https://example.com/é' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://-example.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://example..com' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://example_x.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'https://ex%41mple.com' ] ],
    [ 'site',     'url',                      [ 'site' => 'http://user:p%41ss@[::1]:8080/a?b#c' ] ],
    [ 'count',    'integer',                  [ 'count' => '9223372036854775808' ] ],
    [ 'count',    'integer',                  [ 'count' => '-9223372036854775808' ] ],
    [ 'count',    'integer|max:10',           [ 'count' => '99999999999999999999' ] ],
    [ 'zip',      'regex:/^[0-9]{,4}$/',      [ 'zip' => '12' ] ],
    [ 'code',     'regex:/^[A-Z]{2, 3}$/',    [ 'code' => 'NL' ] ],
    [ 'year',     'regex:/^\d{ 4 }$/',        [ 'year' => '1234' ] ],
    [ 'code',     'regex:/^{[a-z]+}$/',       [ 'code' => '{ab}' ] ],
    [ 'code',     'regex:/^{[a-z]+}$/',       [ 'code' => 'ab' ] ],
    [ 'code',     'regex:/^[{,]\{,2}$/',      [ 'code' => ',{,2}' ] ],
  ];

  $parity = [];

  foreach ( $padCases as $padCase ) {

    [ $field, $rules, $data ] = $padCase;

    $messages = $padCase [3] ?? [];

    $parity [] = [ 'field'  => $field,
                      'data'   => (object) $data,
                      'server' => padValidate ( [ $field => $rules ], $data, $messages ) [$field] ?? '',
                      'client' => padValidateClient ( [ $field => $rules ], $messages ) ];

  }

  $padOutputType = 'json';
  $padExpose     = [ 'parity' ];

?>
