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