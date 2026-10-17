<?php

  // A path or a prefix into a JsonSerializable reads the data its jsonSerialize answers, the
  // one view {^user} and {user}..{/user} have: read by its public properties, {$user.name}
  // printed Ann where {^user} wrote ANN, and {$user.password_hash} the hash its JSON leaves out.

  class padCaseJsonPathUser implements JsonSerializable {

    public $name          = 'Ann';
    public $password_hash = 'SECRET-HASH';

    function jsonSerialize (): mixed { return [ 'name' => strtoupper ( $this -> name ) ]; }

  }

  $user  = new padCaseJsonPathUser;
  $users = [ 'first' => new padCaseJsonPathUser ];

?>
