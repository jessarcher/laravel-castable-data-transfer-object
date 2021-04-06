# Laravel Castable Data Transfer Object

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jessarcher/laravel-castable-data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/jessarcher/laravel-castable-data-transfer-object)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/jessarcher/laravel-castable-data-transfer-object/PHP%20Composer?label=tests)](https://github.com/jessarcher/laravel-castable-data-transfer-object/actions/workflows/php.yml?query=branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jessarcher/laravel-castable-data-transfer-object.svg?style=flat-square)](https://packagist.org/packages/jessarcher/laravel-castable-data-transfer-object)

Laravel is awesome. Spatie's [data transfer object](https://github.com/spatie/data-transfer-object) package for PHP is awesome. They're already good friends, but now they're taking their relationship to the next level 💕

Have you ever wanted to cast your JSON columns to a value object?

This package gives you an extended version of Spatie's `DataTransferObject` class, called `CastableDataTransferObject`.

Under the hood it implements Laravel's [`Castable` interface](https://laravel.com/docs/8.x/eloquent-mutators#castables) with a Laravel [custom cast](https://laravel.com/docs/8.x/eloquent-mutators#custom-casts) that handles serializing between the `DataTransferObject` (or a compatible array) and your JSON database column.

For an in-depth explanation of what it's actually doing and the motivation behind it, check out [the blog post that spawned it](https://jessarcher.com/blog/casting-json-columns-to-value-objects/).

This package has also been featured on [Laravel News](https://laravel-news.com/laravel-castable-data-transfer-object)!

## Installation

You can install the package via composer:

```bash
composer require jessarcher/laravel-castable-data-transfer-object
```

## Usage

### 1. Create your `CastableDataTransferObject`

Check out the readme for Spatie's [data transfer object](https://github.com/spatie/data-transfer-object) package to find out more about what their `DataTransferObject` class can do.

``` php
namespace App\Values;

use JessArcher\CastableDataTransferObject\CastableDataTransferObject;

class Address extends CastableDataTransferObject
{
    public string $street;
    public string $suburb;
    public string $state;
}
```

(Note: I like to put these in `App\Values` because I'm using them as a value object and not just a plain DTO. Feel free to put it anywhere you like!)

### 2. Configure your Eloquent attribute to cast to it:

Note that this should be a `jsonb` or `json` column in your database schema.

```php
namespace App\Models;

use App\Values\Address;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        'address' => Address::class,
    ];
}
```

And that's it! You can now pass either an instance of your `Address` class, or even just an array with a compatible structure. It will automatically be cast between your class and JSON for storage and the data will be validated on the way in and out.

```php
$user = User::create([
    // ...
    'address' => [
        'street' => '1640 Riverside Drive',
        'suburb' => 'Hill Valley',
        'state' => 'California',
    ],
])

$residents = User::where('address->suburb', 'Hill Valley')->get();
```

But the best part is that you can decorate your class with domain-specific methods to turn it into a powerful value object.

```php
$user->address->toMapUrl();

$user->address->getCoordinates();

$user->address->getPostageCost($sender);

$user->address->calculateDistance($otherUser->address);

echo (string) $user->address;
```

### Controlling serialization

You may provide caster with flags to be used for serialization by adding `CastUsingJsonFlags` attribute to your object:

```php
use JessArcher\CastableDataTransferObject\CastableDataTransferObject;
use JessArcher\CastableDataTransferObject\CastUsingJsonFlags;

#[CastUsingJsonFlags(encode: JSON_PRESERVE_ZERO_FRACTION)]
class Address extends CastableDataTransferObject {}
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email jess@jessarcher.com instead of using the issue tracker.

## Credits

- [Jess Archer](https://github.com/jessarcher)
- [Jeremiasz Major](https://github.com/jrmajor)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
