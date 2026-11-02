<?php

  // A list section whose data= finds no rows - a query that found none, a store before its
  // first row - is a miss: its @else@ shows, and a single tag writes nothing; the items written
  // between its tags still make the section. It stopped the page under the strict check with
  // "the {section:faq} has no questions", which is for a template that gives no items at all.

  $plans  = [];
  $quotes = [];

?>
