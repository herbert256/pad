<?php

  $q = 'cache ttl';

  $body = 'Some parts of a page cost more to render than others: a list of the best selling '
        . 'products, a menu built from the database, a report over a year of orders. The '
        . 'fragment cache keeps the rendered text of such a section for a number of seconds and '
        . 'serves it again on the next request without running the tags inside it. Name the '
        . 'section, give it a ttl, and forget it when what it shows has changed.';

?>
