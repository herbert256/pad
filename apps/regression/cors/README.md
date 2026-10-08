# Regression: cross-origin requests

## Introduction

Regression test for `$padCors` (pad/lib/cors.php). The index fetches the data page as other
origins would and shows the CORS headers of every answer: the listed origin gets itself back
with credentials, the exposed header and `Vary: Origin`; another origin, or none, gets no
Access-Control header; a preflight is answered 204 at once - with the methods, headers and
max age for the listed origin, bare for another - before the CSRF check; a cross-origin post
without its token is still refused 403; and with `'*'` the answer is `*`.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the data page as several origins and shows the headers |
| `data.pad` | The page fetched |
| `_config/config.php` | `_common` off, CSRF on, one origin allowed; `?any` every origin |
