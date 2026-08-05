<?php

  // array: - looks the name up as a field but insists on an array, so {array:items} yields the
  // data behind a tag rather than a scalar. Missing or scalar fields come back as [].

  return padArrayValue ($name, TRUE);

?>