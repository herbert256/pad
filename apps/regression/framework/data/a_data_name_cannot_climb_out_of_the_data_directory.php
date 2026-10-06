<?php

  // A data name is a file in a _data directory. One that climbs out of it with ../ - here
  // to a .php file beside this case - is no data name at all: a name a visitor sent would
  // otherwise include and run any .php file on the disk. padDataFileName is the one lookup
  // behind data=, local:, the type sniff and {chart data=}, so it is asked directly; data=
  // with such a name is an error of its own (errors syntax/a_data_value_does_not_leave_the_data_directory).

  $name  = '../data/_outside';
  $found = padDataFileName ( $name ) === '' ? 'refused' : 'found';

?>
