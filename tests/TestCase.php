<?php

namespace Avlyalin\SberbankAcquiring\Tests;

use Avlyalin\SberbankAcquiring\Providers\AcquiringServiceProvider;
use Avlyalin\SberbankAcquiring\Models\AcquiringPayment;
use Avlyalin\SberbankAcquiring\Models\AcquiringPaymentOperation;
use Avlyalin\SberbankAcquiring\Models\ApplePayPayment;
use Avlyalin\SberbankAcquiring\Models\GooglePayPayment;
use Avlyalin\SberbankAcquiring\Models\SamsungPayPayment;
use Avlyalin\SberbankAcquiring\Models\SberbankPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->registerEloquentFactories($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [AcquiringServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * @param Application $app
     */
    private function setUpDatabase(Application $app)
    {
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/laravel/laravel/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Регистрация фабрик
     *
     * @param Application $app
     */
    private function registerEloquentFactories(Application $app)
    {
        $factory = $app->make(Factory::class);
        $factory->load(__DIR__ . '/../database/factories');
        $factory->load(__DIR__ . '/../vendor/laravel/laravel/database/factories');
    }

    protected function createUser(array $attributes = [])
    {
        $userModel = config('sberbank-acquiring.user.model');
        return factory($userModel)->create($attributes);
    }

    protected function createAcquiringPayment(array $attributes = [], ...$states): AcquiringPayment
    {
        return factory(AcquiringPayment::class)->states($states)->create($attributes);
    }

    protected function createSberbankPayment(array $attributes = []): SberbankPayment
    {
        return factory(SberbankPayment::class)->create($attributes);
    }

    protected function createApplePayPayment(array $attributes = []): ApplePayPayment
    {
        return factory(ApplePayPayment::class)->create($attributes);
    }

    protected function createSamsungPayPayment(array $attributes = []): SamsungPayPayment
    {
        return factory(SamsungPayPayment::class)->create($attributes);
    }

    protected function createGooglePayPayment(array $attributes = []): GooglePayPayment
    {
        return factory(GooglePayPayment::class)->create($attributes);
    }

    protected function createAcquiringPaymentOperation(array $attributes = []): AcquiringPaymentOperation
    {
        return factory(AcquiringPaymentOperation::class)->create($attributes);
    }

    protected function mockAcquiringPayment(string $method, $returnValue)
    {
        $acquiringPayment = \Mockery::mock(AcquiringPayment::class . "[$method]");
        $acquiringPayment->shouldReceive($method)->andReturn($returnValue);
        return $acquiringPayment;
    }

    protected function mockSberbankPayment(string $method, $returnValue)
    {
        $sberbankPayment = \Mockery::mock(SberbankPayment::class . "[$method]");
        $sberbankPayment->shouldReceive($method)->andReturn($returnValue);
        return $sberbankPayment;
    }

    protected function mockAcquiringPaymentOperation(string $method, $returnValue)
    {
        $operation = \Mockery::mock(AcquiringPaymentOperation::class . "[$method]");
        $operation->shouldReceive($method)->andReturn($returnValue);
        return $operation;
    }

    /**
     * Записывает значение в свойство объекта
     *
     * @param $object
     * @param $property
     * @param $value
     */
    protected function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
