<?php

  // The form of a page part counts on from the forms of the page around it, and writes the
  // page the request built as its place - the place this post sends back. Only that form
  // refills: the page's own form of the same name stays empty.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'c', 'padFormAt' => 'tags/a_post_from_a_form_in_a_page_part_refills_that_form_alone:2', 'text' => 'abc' ];

  padValidate ( [ 'text' => 'required|min:5' ] );

?>
