# Chameleon Shop Creator

This command-line tool facilitates the creation of a new Chameleon Shop, automating tasks such as system requirement
checks, cloning from GitHub, and setting up the project with Composer.

Usage Animation via [asciinema](![asciinema](https://github.com/asciinema/asciinema)):
![Alt Text](https://github.com/kzorluoglu/chameleon-installer/blob/main/asciinema-min.gif)

_cast File Converted via https://dstein64.github.io/gifcast/_

## Requirements

- PHP 8.1 or higher
- Required PHP Extensions: curl, mbstring, mysqli, pdo_mysql, zip, tidy, intl, gd

## Installation

Clone this repository and run `composer install` to set up the command.

## Usage

You can install the Chameleon Shop Installer globally using Composer:

```bash
composer global require kzorluoglu/chameleon-installer
````

### Ensure the Command is in Your PATH

After installing the Chameleon Installer, ensure the Composer global `bin` directory is in your PATH so the Composer commands can be executed from anywhere.

If it isn't, add the following to your shell configuration file (e.g., `.bashrc` or `.zshrc`):

```bash
echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc
```


# Creating a New Project

Once installed, you can create a new Chameleon project using:

```bash
chameleon create /path/to/your/new/shop 7.2.x # or 7.1.x or master for development
````

Replace `/path/to/your/new/shop` with the desired directory for your new Chameleon Shop.

## Features

* Checks PHP version and extensions.
* Clones Chameleon Shop from GitHub.
* Runs Composer in the project directory.
* Offers database setup and import options, with flexibility in handling configuration settings.

## Contributing

Your contributions to enhance and improve this tool are greatly appreciated. Please adhere to standard open-source
contribution guidelines.

## License

This software is open-source, licensed under the MIT license.

## Development

For local testing:

* Install Composer.
* Clone the repo.
* Run `composer install`.
* Make `bin/chameleon` executable (`chmod +x`).
* Test with `./bin/chameleon`.