---
title: Using multiple logs
weight: 5
---

## The default log

Without specifying a log name, the activities will be logged on the default log.

```php
activity()->log('hi');

$lastActivity = Activity::all()->last();

$lastActivity->log_name; //returns 'default';
```

You can specify the name of the default log in the `default_log_name` key of the config file.

## Specifying a log

You can specify the log on which an activity must be logged by passing the log name to the `activity` function:

```php
activity('other-log')->log("hi");

Activity::all()->last()->log_name; //returns 'other-log';
```

## Specifying a log for each model

By default, the `LogsActivity` trait uses `default_log_name` from the config file to write the logs. To customize the log's name for each model, call the `useLogName()` method when configuring the `LogOptions`.

```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('custom_log_name_for_this_model');
}
```

## Using a backed enum as log name

Instead of plain strings you can pass a string- or int-backed enum as the log name. The enum's
`value` is stored in the database:

```php
enum LogName: string
{
    case Orders = 'orders';
    case Auth = 'auth';
}

activity(LogName::Orders)->log('hi');

Activity::all()->last()->log_name; //returns 'orders'
```

By default `log_name` is read back as the stored string. If you want it hydrated back into an
enum, set the enum class in the `default_log_enum` key of the config file:

```php
// config/activitylog.php
'default_log_enum' => \App\Enums\LogName::class,
```

```php
Activity::all()->last()->log_name; //returns LogName::Orders
```

> **Note:** `default_log_enum` is global — only one enum can be configured. Any `log_name` that
> isn't one of that enum's values (for example the `default` log) is returned as a plain string,
> so `log_name` may be either an enum or a string. Compare against `->value` (or normalize) when
> you mix enum and non-enum log names.

## Retrieving activity

The `Activity` model is just a regular Eloquent model that you know and love:

```php
Activity::where('log_name', 'other-log')->get(); //returns all activity from the 'other-log'
```

There's also an `inLog` scope you can use:

```php
Activity::inLog('other-log')->get();

//you can pass multiple log names to the scope
Activity::inLog('default', 'other-log')->get();

//passing an array is just as good
Activity::inLog(['default', 'other-log'])->get();

//backed enums work here too
Activity::inLog(LogName::Orders)->get();
Activity::inLog(LogName::Orders, LogName::Auth)->get();
```
