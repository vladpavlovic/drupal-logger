A simple PSR-3 implementation of a logger for Drupal watchdog calls.

# Details

The PSR-3 parameter ```$context``` is passed to watchdog as variables, for use as placeholders.

```
    class MyClass
    {
        public function myMethod($param)
        {
            $this->logger->warning('Param value was @param', ['@param' => $param]);

            return $param;
        }
    }
```

The watch dog type is set as the function or class method that called the logging code. In the example above, the watchdog type is set as *MyClass::myMethod*.