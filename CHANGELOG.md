CHANGELOG
=========

A [BC BREAK] means the update will break the project for many reasons:

* new mandatory configuration
* new dependencies
* class refactoring
* renaming

### 2.0

* [BC BREAK] changed minimum required versions:
  - minimum Symfony version is 5.0
  - minimum PHP version is 8.0
  - minimum Sonata Admin version is 4.0
* [BC BREAK] renamed all "pix" references to "codeclipse" (project name, namespaces, parameters, js events etc.)
* added Polish translations file

### 0.2-dev

* [BC BREAK] use translatable keys instead of plain text
* [BC BREAK] change file extensions from `*.xlf` to `*.xliff`
