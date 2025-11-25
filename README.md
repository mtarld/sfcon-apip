# API Platform, the good way

An API Platform decoupling implementation example

## Installation

1. Clone the repository

2. Install dependencies:
```bash
composer install
```

3. Setup databases:
```bash
bin/console doctrine:schema:update --force
bin/console doctrine:schema:update --force --env test
```

## Usage

### Development Server

Start the Symfony development server:
```bash
symfony server:start
```

Then, play with your app at http://127.0.0.1:8000/api

### Running Tests

```bash
php bin/phpunit
```
