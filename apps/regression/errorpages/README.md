# Regression: custom error pages

## Introduction

Regression test for `$padErrorPages` (pad/lib/errorPage.php): an application with an
`_errors/` directory answers a request that cannot go on with its own page, keeping the
status. The index fetches every kind and shows status, content type and body: a page that is
not there (also `?sitemap.xml` and `?up` with those answers off, and `_errors/404` itself,
which is no page), a guard's refusal, a post without its CSRF token, `padAbort` through the
`4xx` page, a failing page's 500 - diagnostics are off, so every request counts as a
visitor's - and an error page that fails itself, whose answer is the plain line.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches every error and shows status, type and body |
| `_errors/_inits.pad` | The frame of the error pages |
| `_errors/404.pad`, `403.pad`, `500.pad` | The pages of one status each |
| `_errors/4xx.pad` | Every other client error |
| `_errors/418.pad` | A page that fails: the plain line answers |
| `admin/_guard.php`, `admin/panel.pad` | A guard that refuses |
| `slow.php`, `gone.php`, `teapot.php` | `padAbort` with 429, 410 and 418 |
| `broken.pad` | A page that fails: the 500 page |
| `about.pad` | A page that is there |
| `_config/config.php` | `_common` off, CSRF on, diagnostics off |
