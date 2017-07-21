# iCal creator for Laravel
It's a simple little thing, but it keeps comming back so I made a package for it.
This package contains a single class which generates an iCal (.ics) file for your 
agenda to import. 

## Functionality
- Create stream or get the content
- Alarm functionality
- Visibility for Microsoft and others combined to "visibility"
- Title, summary and description are all custom but fallback on title.
- Start date/end data gets the timezone from Laravel config, but can be overwritten if needbe.
- Set an alarm

## Streaming

```php
use Thorazine\Ics\Ics;

$ical = new Ics([
	'title' => 'Test Ics', 
	'startDate' => '2017-07-21 12:00:00', 
	'endDate' => '2017-07-21 14:00:00',
]);

$ical->stream();
```


## Save to file

```php
use Thorazine\Ics\Ics;

$ical = new Ics([
	'title' => 'Test Ics', 
	'startDate' => '2017-07-21 12:00:00', 
	'endDate' => '2017-07-21 14:00:00',
]);

file_put_contents('some-filename.ics', $ical->get());
``` 


## Options
Options are inserted in the array when creating a new Ics class

| key | Mandatory | Type | Possible values |
| --- | --- | --- | --- |
| title | true | string | Any |
| startDate | true | timestamp | Any timestamp as excepted by strtotime() |
| endDate | true | timestamp | Any timestamp as excepted by strtotime() |
| summary | false | string | Any |
| description | false | string | /n for new line |
| location | false | string | Any |
| timezone | false | timezone | Any timezone known to PHP |
| alarm | false | string | [integer][D or H or M] (M = minute, H = hour, D = Day) |
| availibility | false | boolean | true = opaque and out of office, false = transparant and free |
