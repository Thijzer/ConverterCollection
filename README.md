# Converter Collection

--- experimental ----

## Installation

We need to clone this repo to a folder (spoon2twigconverter) in our project
```bash
git clone https://github.com/thijzer/ConverterCollection spoon2twigconverter
```

Now we need to install composer for that generated folder (spoon2twigconverter)
```bash
cd spoon2twigconverter
composer install
```

After you have converted your files you can savely remove the spoon2twigconverter.

## Example for spoon2twig
Now we can convert all SpoonTemplates to Twig templates
```bash
bin/hercules spoon2twig --all --source '/users/YOUR-USERNAME/Documents/my-fork-project/src'
```
> NOTE: Change 'YOUR-USERNAME' by your own.

### Spoon2Twig commands

#### Usage

Running the Spoon2Twig Converter.
Run every command starting with `bin/hercules spoon2twig`

#### Commands

>  WARNING:
>
>  Default no source dir needs to be provided.
>  But I don't recommend installing the converter inside your project Folder
>  so for now use the --source option for commands (--all, --theme, --module)

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

Converts a single file in the project (relative)
```php
--file /path/to/filename.tpl
```

#### Optional arguments

With this argument you specify the root (src) folder of your Project
```php
--source /path/to/project/source/src
```

Force the command with overwrite options
```php
-f
```
