<?php

  // Sample data for JSON example
  $userData = [
    ['id' => 1, 'name' => 'Alice', 'role' => 'Developer'],
    ['id' => 2, 'name' => 'Bob', 'role' => 'Designer'],
    ['id' => 3, 'name' => 'Charlie', 'role' => 'Manager']
  ];

  // Encode to JSON with HTML escaping
  $usersJson = htmlspecialchars(json_encode($userData), ENT_QUOTES, 'UTF-8');

?>