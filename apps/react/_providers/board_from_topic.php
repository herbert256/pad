<?php

  return db ( "RECORD * FROM forum_boards WHERE id={0}", 
              [ $padProviders ['topic'] ['board_id'] ] 
            );

?>