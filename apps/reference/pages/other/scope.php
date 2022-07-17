<?php

  $php3 = db('array * from staff');

  $php1 ['banaan'] = '123';

  $php1 ['bob'] ['phone'] = '555-3425';
  $php1 ['bob'] ['age'] = '33';

  $php1 ['jim'] ['phone'] = '555-4364';
  $php1 ['jim'] ['age'] = '44';

  $php1 ['joe'] ['name']  = 'joe';
  $php1 ['joe'] ['age'] ['bob'] ['phone'] = '22';

  $php1 ['jerry'] ['name']  = 'jerry';
  $php1 ['jerry'] ['phone'] = '555-4973';

  $php1 ['fiets'] = '789';

  $php2 = [
    [ 'name' => 'bob',   'phone' => '555-3425'],
    [ 'name' => 'jim',   'phone' => '555-4364'],
    [ 'name' => 'joe',   'phone' => '555-3422'],
    [ 'name' => 'jerry', 'phone' => '555-4973']
  ];

?>