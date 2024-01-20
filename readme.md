# Chameleon Shop Creator

This command-line tool facilitates the creation of a new Chameleon Shop, automating tasks such as system requirement
checks, cloning from GitHub, and setting up the project with Composer.

## Requirements

- PHP 8.1 or higher
- Required PHP Extensions: curl, mbstring, mysqli, pdo_mysql, zip, tidy

## Installation

Clone this repository and run `composer install` to set up the command.

## Usage

You can install the Chameleon Shop Installer globally using Composer:

```bash
composer global require kzorluoglu/chameleon-installer
````

# Creating a New Project

Once installed, you can create a new Chameleon project using:

```bash
chameleon create /path/to/your/new/shop
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