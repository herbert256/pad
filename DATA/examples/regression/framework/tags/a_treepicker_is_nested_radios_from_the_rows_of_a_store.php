<?php

  // Flat rows, each naming its parent - empty for a root, and a root may come after its
  // children: the tree is made from the roots down, its radios in that order, the value of
  // each its id and the text its name; value= chooses one, and the button shows its path.

  $categories = [
    [ 'id' => 2, 'parent' => 1,  'name' => 'Fiction' ],
    [ 'id' => 1, 'parent' => '', 'name' => 'Books' ],
    [ 'id' => 3, 'parent' => 2,  'name' => 'Crime' ],
    [ 'id' => 4, 'parent' => 2,  'name' => 'Fantasy' ],
    [ 'id' => 6, 'parent' => '', 'name' => 'Music' ]
  ];

?>
