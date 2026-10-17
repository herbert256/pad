<?php

  // An object that says what its data is - JsonSerializable - is that data, to {^user} as to
  // json_encode: read by its public properties, the hash it leaves out was written into the page.

  class padCaseJsonUser implements JsonSerializable {

    public $name          = 'Ann';
    public $password_hash = 'SECRET-HASH';

    function jsonSerialize (): mixed { return [ 'name' => $this -> name ]; }

  }

  $user = new padCaseJsonUser;

?>
