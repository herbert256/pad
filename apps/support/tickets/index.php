<?php

  requireLogin();

  $title = 'My Tickets';
  $showAll = ($role === 'admin');
  if ($showAll) $title = 'All Tickets';

?>