<?php

  // A JsonSerializable whose jsonSerialize answers the object itself - return $this - is its
  // public properties, as json_encode writes it: it stayed an object, so {me}..{/me} was no
  // tag, {^me} was not found and a list of them printed the tag's name per row.

  class padCaseJsonSelf implements JsonSerializable {

    public    $name   = 'Ann';
    protected $secret = 'hidden';

    function jsonSerialize (): mixed { return $this; }

  }

  $me   = new padCaseJsonSelf;
  $list = [ new padCaseJsonSelf, new padCaseJsonSelf ];
  $enc  = json_encode ( $me );

?>
