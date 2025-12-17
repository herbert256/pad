&nbsp;
# PAD - PHP Application Driver

&nbsp;
&nbsp;

## Concept

PAD is an [inversion of control](https://en.wikipedia.org/wiki/Inversion_of_control) PHP application driver.
 
PAD:  
\- first executes the application PHP code,  
\- then reads the application HTML markup code,  
\- merges both and send the result to the browser.

There is no PAD code in the application PHP code at all !

&nbsp;
&nbsp;

## Hello World
&nbsp;
`hello.php` - The application PHP file:
```php
<?php

  $hi = 'Hello World !';

?>
```

`hello.pad` - The PAD template:
```html
<html>
  <body>
    <h1>{$hi}!</h1>
  </body>
</html>
```
&nbsp;
`Browser` - The result in your web browser:
### Hello World !

&nbsp;
&nbsp;

## Don't call us, we'll call you

[Sugarloaf - Don't call us, we'll call you.](https://www.youtube.com/watch?v=i4njPe2_rho&t=45s)

&nbsp;
&nbsp;

## Directory Structure

```
pad/
├── pad/      # PAD framework core
├── apps/     # PAD applications
├── www/      # Web server entry points
└── DATA/     # Runtime data (logs, cache, dumps)
```

### pad/

The PAD framework itself - the template engine, tag processors, expression evaluator, and all supporting modules.

- See [pad/README.md](pad/README.md) for complete framework PHP sources

### apps/

PAD applications. Each subdirectory is a self-contained application with its own templates, data files, and libraries.

See [apps/README.md](apps/README.md) for application development guidelines.

### www/

Web server document root files. Contains PHP entry points that configure `APP` and `DAT` constants and include the PAD framework.

See [www/README.md](www/README.md) for web server setup.

### DATA/

Runtime data directory for generated files:
- Cache files
- Log files
- Debug dumps
- Session data
- Temporary files

This directory should be writable by the web server and excluded from version control.

&nbsp;
&nbsp;

## Additional Documentation

- [docs/README.md](docs/README.md) - Full documentation index

&nbsp;
&nbsp;

## License

PAD is licensed under the GNU General Public License v3.0. See [LICENSE.md](docs/LICENSE.md) for details.

- See [docs/PAD.md](docs/PAD.md) for complete framework documentation.
