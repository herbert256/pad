<?php
return padGame('battleship','Fleet search',['size'=>padGameInt('size',8,6,10)],'<p>Find the hidden fleet: ships of length 4, 3 and 2. Ships do not overlap and may touch. Choose a coordinate; repeated shots cost no turn.</p>');
