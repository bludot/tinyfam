# tinyfam

A small PHP web framework, written from scratch — routing, models, middleware,
views and the container that ties them together.

Not built on Laravel or Symfony: the point was to write the pieces a framework
is made of and see how they fit.

## What is inside

| Path | What it is |
|---|---|
| `Core/Route`, `Core/Helpers/RouterTrie.php` | routing, matched through a trie |
| `Core/Helpers/Trie/` | the trie itself, separately testable |
| `Core/Middleware/` | middleware and the collection that runs it |
| `Core/Model.php`, `Core/Database/` | models and the database provider |
| `Core/Facade.php`, `Core/Facades/` | the static-looking accessors, Laravel-style |
| `Core/Cache.php`, `Core/Session.php` | caching and sessions |
| `Core/Inflection.php` | pluralisation, for mapping models to tables |
| `Bootstrap.php`, `Core/Boot.php` | start-up and wiring |

The trie-based router is the piece worth reading — the same approach appears
again in [gorouter](https://github.com/bludot/gorouter), in Go.

## Running it

Requires PHP and a web server pointed at the project root; `.htaccess` handles
the front-controller rewrite.

```sh
cp .env.example .env      # database and app settings
```

Deployment manifests for Kubernetes live in
[tinyfam-kube](https://github.com/bludot/tinyfam-kube). `.gitlab-ci.yml` holds
the pipeline it was built with.

## Status

From 2020–2021, and not maintained.
