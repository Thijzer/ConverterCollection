# ConverterCollection

--- experimental ----

## Usage

A the moment it's limited to Spoon2Twig conversions.

## Installation

At the moment you best download the zip file and `unzip` it in your
project `root folder`.

Run every command starting with `php Spoon2Twig/Spoon2TwigCommand.php`

### Spoon2Twig commands

Converts every Spoon .tpl in the project
```php
-- all
```

Converts a Theme based on name
```php
--theme <themeName>
```

Converts a module based on name
```php
--module <moduleName>
```

Converts a single file in the project
```php
--file src/path/to/filename.tpl
```

Additional commands

Force the command with overwrite options
```php
-f
```
