<?php

  return db ( "RECORD id, username, email, role, created_at FROM users WHERE id={0}", 
            [ $padProviders ['topic'] ['user_id'] ] );

?>