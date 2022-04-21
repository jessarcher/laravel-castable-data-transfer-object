<?php

namespace JessArcher\CastableDataTransferObject\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JessArcher\CastableDataTransferObject\CastableDataTransferObject;
use JessArcher\CastableDataTransferObject\CastUsingJsonFlags;
use TypeError;

class CastableDataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_casts_arrays_to_json()
    {
        User::factory()->create([
            'address' => [
                'street' => '1640 Riverside Drive',
                'suburb' => 'Hill Valley',
                'state' => 'California',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'address->street' => '1640 Riverside Drive',
            'address->suburb' => 'Hill Valley',
            'address->state' => 'California',
        ]);
    }

    /** @test */
    public function it_casts_data_transfer_objects_to_json()
    {
        User::factory()->create([
            'address' => new Address([
                'street' => '1640 Riverside Drive',
                'suburb' => 'Hill Valley',
                'state' => 'California',
            ]),
        ]);

        $this->assertDatabaseHas('users', [
            'address->street' => '1640 Riverside Drive',
            'address->suburb' => 'Hill Valley',
            'address->state' => 'California',
        ]);
    }

    /** @test */
    public function it_casts_json_to_a_data_transfer_object()
    {
        $user = User::factory()->create([
            'address' => [
                'street' => '1640 Riverside Drive',
                'suburb' => 'Hill Valley',
                'state' => 'California',
            ],
        ]);

        $user = $user->fresh();

        $this->assertInstanceOf(Address::class, $user->address);
        $this->assertEquals('1640 Riverside Drive', $user->address->street);
        $this->assertEquals('Hill Valley', $user->address->suburb);
        $this->assertEquals('California', $user->address->state);
    }

    /** @test */
    public function it_throws_exceptions_for_incorrect_data_structures()
    {
        $this->expectException(TypeError::class);

        User::factory()->create([
            'address' => [
                'bad' => 'thing',
            ],
        ]);
    }

    /** @test */
    public function it_rejects_invalid_types()
    {
        $this->expectException(InvalidArgumentException::class);

        User::factory()->create([
            'address' => 'string',
        ]);
    }

    /** @test */
    public function it_handles_nullable_columns()
    {
        $user = User::factory()->create(['address' => null]);

        $this->assertDatabaseHas('users', ['address' => null]);

        $this->assertNull($user->refresh()->address);
    }

    /** @test */
    public function cast_uses_specified_flags_for_arrays()
    {
        $user = User::factory()->create([
            'with_flags' => ['floatValue' => 52.0],
            'without_flags' => ['floatValue' => 20.0],
        ]);

        $user = User::whereId($user->id)->toBase()->first();

        $this->assertSame('{"floatValue":52.0}', $user->with_flags);
        $this->assertSame('{"floatValue":20}', $user->without_flags);
    }

    /** @test */
    public function cast_uses_specified_flags_for_data_transfer_objects()
    {
        $user = User::factory()->create([
            'with_flags' => new DataTransferObjectWithFlags(floatValue: 52.0),
            'without_flags' => new DataTransferObjectWithoutFlags(floatValue: 20.0),
        ]);

        $user = User::whereId($user->id)->toBase()->first();

        $this->assertSame('{"floatValue":52.0}', $user->with_flags);
        $this->assertSame('{"floatValue":20}', $user->without_flags);
    }

    /** @test */
    public function it_passes_null_values_to_caster_when_nullable_cast_attribute_present()
    {
        $user = User::factory()->create(['settings' => null]);

        $this->assertDatabaseHas('users', ['settings' => null]);

        $this->assertInstanceOf(Settings::class, $user->refresh()->settings);
        $this->assertEquals('Default', $user->settings->title);
    }
}

class Address extends CastableDataTransferObject
{
    public string $street;
    public string $suburb;
    public string $state;
}

#[CastUsingJsonFlags(encode: JSON_PRESERVE_ZERO_FRACTION)]
class DataTransferObjectWithFlags extends CastableDataTransferObject
{
    public float $floatValue;
}

class DataTransferObjectWithoutFlags extends CastableDataTransferObject
{
    public float $floatValue;
}

class Settings extends CastableDataTransferObject
{
    public string $title = 'Default';
}

class User extends Model
{
    use HasFactory;

    protected $casts = [
        'address' => Address::class,
        'with_flags' => DataTransferObjectWithFlags::class,
        'without_flags' => DataTransferObjectWithoutFlags::class,
        'settings' => Settings::class . ':nullable',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'address' => [
                'street' => $this->faker->streetAddress,
                'suburb' => $this->faker->city,
                'state' => $this->faker->state,
            ],
        ];
    }
}
