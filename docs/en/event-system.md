# Usage

The system is configured in a _config.php with registrering events with
event listeners.

```php
<?php
    Event::register_event('UpdateEvent', 'UpdateEventListener'):
```

This means that the event UpdateEvent gets fired it will notify all objects that
implements the interface UpdateEventListener.

The events are most commonly fired in an onAfterWrite or onBeforeDelete. Here is
how a DataObject would trigger a UpdateEvent:

```php
<?php
    public function onAfterWrite() {
        parent::onAfterWrite();
        Event::fire_event(new UpdateEvent($this));
    }
```

The UpdateEvent in this case takes an reference to the DataObject that fires the
it.

The Event system then calls every implementator of the UpdateEventListener
with the method Object#UpdateEvent(Event $event). This gives the
implementator a reference to the original triggerer.

## Example implementation

Here is an silly example on how this event system would be used to flush a cache
on every Employee that has_one Company and that Company get's updated.

This could be handled in the Company but it's not very encapsulated in that way
and with a couple of different events going on, it get's quiet complicated to
keep track of who is doing what to whom.

_config.php

```php
<?php
	Event::register_event('UpdateEvent', 'CompanyEventListener'):
```

UpdateEvent.php

```php
<?php
class UpdateEvent {

	protected $object;

	public function __construct($object) {
		$this->object = $object;
	}

	public function getObject() {
		return $this->object;
	}
}
```

CompanyEventListener.php

```php
<?php
interface CompanyEventListener {
	public function companyEvent(UpdateEvent $event);
}
```

Company.php

```php
<?php
class Company extends DataObject {

	public function onAfterWrite() {
		parent::onAfterWrite();
		Event::fire_event(new UpdateEvent($this));
	}
}
```

Employee.php

```php
<?php
class Employee extends DataObject implements CompanyEventListener {

	/**
	* Flushes our special super cool cache on the employees when a Company gets
	* updated.
	*/
	public function companyEvent(UpdateEvent $event) {
		$company = $event->getObject();
		$employees = DataObject::get('Employee', '"CompanyID" = \''.$company->ID.'\'');
		foreach($employees as $employee) {
			$employee->flushSuperCache();
		}
	}
}
```