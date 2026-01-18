&nbsp;
# PAD - PHP Application Driver

&nbsp;
&nbsp;

## Concept

PAD is an [inversion of control](https://en.wikipedia.org/wiki/Inversion_of_control) PHP application driver.
 
PAD:  
\- first executes the application PHP code, <br>
\- then reads the application HTML markup code (aka PAD template), <br>
\- merges both and send the result to the browser.

There is no PAD code in the application PHP code at all !

&nbsp;
&nbsp;

## Hello World example
&nbsp;
`hello.php` - The application PHP file:
```php
<?php

  $hi = 'Hello World !';

?>
```

`hello.pad` - The application PAD template:
```html
<html>
  <body>
    <h1>{$hi}</h1>
  </body>
</html>
```

The data that is send to the browser:
```html
<html>
  <body>
    <h1>Hello World !</h1>
  </body>
</html>
```

&nbsp;
&nbsp;

## Don't call us, we'll call you

[Sugarloaf - Don't call us, we'll call you.](https://www.youtube.com/watch?v=i4njPe2_rho&t=45s)

&nbsp;
&nbsp;

## Directory Structure

```
pad/
├── apps/     # A few PAD applications, including the PAD manual
├── docs/     # Documentation - GitHub Pages
├── md/       # Documentation - GitHub MarkDown
├── pad/      # PAD framework core
├── www/      # Web server entry points
└── DATA/     # Runtime data (logs, cache, dumps)
```

### apps/

PAD applications. Each subdirectory is a PAD application with its own templates, data files, and libraries.

- See [apps/README.md](apps/README.md) for application development guidelines.

### docs/

Complete documentation about PAD, the template options, and so on.

- See [docs/README.md](docs/README.md) for this documentation

### pad/

The PAD framework itself - the template engine, tag processors, expression evaluator, and all supporting modules.

- See [pad/README.md](pad/README.md) for complete framework PHP sources

### www/

Web server document root files. Contains PHP entry points that configure `APP` and `DAT` constants and include the PAD framework.

- See [www/README.md](www/README.md) for web server setup.

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

## License

PAD is licensed under the GNU General Public License v3.0. See [GPL.md](docs/GPL.md) for details.