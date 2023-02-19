# Progi Test

This project resolves technical test made by Progi Enterprise.

With this program you can calculate the maximum value that you can bid
on a car auction with a given budget based in specific fees.

## Setup
- [Installation](#installation)
    - [Clone the repository](#clone-the-repository)
        - [SSH](#ssh)
        - [HTTPS](#https)
- [Usage](#usage)
- [Technical details](#technical-details)

## Installation
### Clone the repository

#### SSH
You must previously set up your public key in your GitHub profile.
For more information please check: https://docs.github.com/en/authentication/connecting-to-github-with-ssh
```
git clone git@github.com:jr-cardona/progi-test.git
```

#### HTTPS
```
git clone https://github.com/jr-cardona/progi-test.git
```

Once cloned the project, just access to it:
```
cd progi-test
```

## Usage

Set up containers
```
docker-compose up -d
```

Interact with application:
```
docker-compose exec progi-test bash -c "php index.php"
```

## Run static analysis and tests

Run static analysis with PHPStan at max level
```
docker-compose exec progi-test bash -c "composer phpstan"
```
Run tests
```
docker-compose exec progi-test bash -c "composer test"
```

## Technical details
### PHPStan
This code complies with actual max level (9) of [PHPStan](https://phpstan.org/). 

This ensures that no code smell is present.

Here’s a brief overview of what’s checked on each level:

0. basic checks, unknown classes, unknown functions, unknown methods called on $this, wrong number of arguments passed to those methods and functions, always undefined variables
1. possibly undefined variables, unknown magic methods and properties on classes with __call and __get
2. unknown methods checked on all expressions (not just $this), validating PHPDocs
3. return types, types assigned to properties
4. basic dead code checking - always false instanceof and other type checks, dead else branches, unreachable code after return; etc.
5. checking types of arguments passed to methods and functions
6. missing typehints
7. partially wrong union types - if you call a method that only exists on some types in a union type
8. report calling methods and accessing properties on nullable types
9. strict about the mixed type - the only allowed operation you can do with it is to pass it to another mixed

### PSR
This code also complies with the following PHP Standard Recommendations:
- [PSR-1](https://www.php-fig.org/psr/psr-1) (Basic Coding Standard)
- [PSR-4](https://www.php-fig.org/psr/psr-4) (Autoloading Standard)
- [PSR-12](https://www.php-fig.org/psr/psr-12) (Extended Coding Style Guide)

### Conventional commits
The standard used to make every commit of this application:
https://www.conventionalcommits.org/en/v1.0.0/