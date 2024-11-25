<html>
  <head>
    <title>Start</title>
  </head>
  <body>
    <h1>Start</h1>
    <ul>
      <li><a href="/">My website</a></li>
      <li><a href="/data">My data</a></li>
      <li><a href="/phpinfo">phpinfo();</a></li>
      <li><a href="/phpsysinfo">phpSysInfo</a></li>
      <li><a href="/server-status">Apache Server Status</a></li>
      <li><a href="/server-info">Apache Server Information</a></li>
      <li><a href="/opcache-gui-1?FILES=1&SORT=4">Opcache (ck-on)</a></li>
      <li><a href="/opcache-gui-2">Opcache (amnuts)</a></li>
      <li><a href="/phpmyadmin">phpMyAdmin</a></li>
      <li><a href="/phpCacheAdmin">phpCacheAdmin</a></li>
      <li><a href="/adminer/adminer.php">Adminer</a></li>
      <li><a href="/adminer/editor.php">Adminer - Editor</a></li>
      <li><a href="/tinyfilemanager">Tiny File Manager</a> ( 'admin' / 'admin@123' ) </li>
      <li><a href="/icecoder">ICEcoder</a></li>
      <li><?php echo '<a href="http://'  . $_SERVER ['HTTP_HOST'] . ':8111/log?path=%2Fvar%2Flog%2Fapache2%2Faccess.log">Apache acces log</a>'; ?></li>
      <li><?php echo '<a href="http://'  . $_SERVER ['HTTP_HOST'] . ':8111/log?path=%2Fvar%2Flog%2Fapache2%2Ferror.log">Apache error log</a>'; ?></li>
      <li><?php echo '<a href="https://' . $_SERVER ['HTTP_HOST'] . ':10000">Webmin</a>'; ?></li>
    </ul>
  </body>
</html>
