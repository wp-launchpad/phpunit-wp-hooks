## PHPUnit WP Hooks

This library is a PHPUnit library aimed to facilitate mocking WordPress hooks during integration testing.

### Install

To install the extension require it:
```bash
composer require wp-launchpad/phpunit-wp-hooks --dev
```

Once you done that then you can use the trait ``MockHooks`` and at the following logic to your base `TestCase` where `my_prefix` is your plugin prefix:
```php
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHooks();
    }

    protected function tearDown(): void
    {
        $this->resetHooks();
        parent::tearDown();
    }


     protected function getPrefix(): string {
        return 'my_prefix';
     }

     protected function getCurrentTest(): string {
        return $this->getName();
     }
```

### Mock hooks

### Register a callback
It is possible to add a callback inside the test class to control the value from a filter.
For that first you need to create a callback in the class:
```php
public function myCallback() {
    return false;
}
```
Then in the dockblock of that callback you can add the `@hook` annotation:
```php
/** 
 * @hook my-event 15
 */ 
```
Where 15 is the priority from the filter. If you forget this part the default priority will be 10.

### Isolate a hook
To isolate a hook to reduce it to a single callback it is possible by adding `@hook-isolated` annotation on the test method:
```php
/**
 * @hook-isolated my-event myCallback 15
 */
```
Where 15 is the priority from the filter. If you forget this part the default priority will be 10.
