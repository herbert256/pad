<?php

  // The forms of a part a page shows twice - one {page} per comment of a list - are told
  // apart as the forms of a loop are: the second part's form is place 2, and a post from it
  // refills that form alone.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'c', 'padFormAt' => 'tags/a_page_part_shown_twice_counts_its_forms_on_from_the_first:2', 'text' => 'abc' ];

  padValidate ( [ 'text' => 'required|min:5' ] );

?>
