# Chameleon Shop Creator

This tool provides a command-line interface for creating a new Chameleon Shop. It checks system requirements, clones the
Chameleon system from GitHub, and runs Composer to install dependencies.

## Requirements

- PHP 8.1 or higher
- Required PHP Extensions: curl, mbstring, mysqli, pdo_mysql, zip, tidy

## Installation

Clone this repository and run `composer install` to set up the command.

## Usage

You can install the Chameleon Shop Installer globally using Composer:

```bash
composer global require your-vendor/chameleon-installer
````

# Creating a New Project

Once installed, you can create a new Chameleon project using:

```bash
chameleon create /path/to/your/new/shop
````

Replace `/path/to/your/new/shop` with the desired directory for your new Chameleon Shop.

## Features

* Verifies PHP version and required extensions.
* Clones the latest version of Chameleon Shop from GitHub.
* Runs Composer install in the project directory.

## Contributing

Your contributions to enhance and improve this tool are greatly appreciated. Please adhere to standard open-source
contribution guidelines.

## License

This software is open-source, licensed under the MIT license.

## Development

To locally test Chameleon application installer follow these steps:

* Install Composer Locally: If not already installed, download and install Composer.
* Clone Repository: Clone the repository where your installer code is located to your local machine.
* Install Dependencies: In the root directory of your cloned repository, run `composer install` to install all necessary
  dependencies.
* Make the Installer Executable: Ensure the bin/chameleon file is executable. You can use `chmod +x bin/chameleon` on
  Unix-based systems.
  *Run the Installer: Execute your installer directly with `./bin/chameleon` followed by any necessary arguments or
  commands. This allows you to test how it behaves.