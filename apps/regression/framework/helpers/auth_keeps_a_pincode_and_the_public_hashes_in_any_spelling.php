<?php

  // padLogin keeps pincode - the postal code of an Indian address - and the public picture
  // hashes in any spelling (emailhash, emailHash, avatar_hash, blurhash), and a hash written
  // together with a word that is no secret's (githash), in the session: taken for secrets,
  // padUser answered NULL for them and {auth}{$pincode}{/auth} ended the page. PINCode,
  // pinhash, pincodehash and userhash stay secrets.

  padLogin ( [ 'id' => 7, 'name' => 'Asha', 'pincode' => '560001', 'emailhash' => 'b58996c504c5638798eb6b511e6f49af',
               'emailHash' => 'b58996c504c5638798eb6b511e6f49af', 'avatar_hash' => 'a1', 'blurhash' => 'LEHV6nWB2yk8', 'githash' => '88ab578',
               'PINCode' => '1234', 'pinhash' => 'p1', 'pincodehash' => 'p2', 'userhash' => 'u1' ] );

  $kept = implode ( ',', array_keys ( padUser () ) );

  padLogout ();

?>
