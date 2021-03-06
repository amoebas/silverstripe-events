<?php
/**
 * Base class that all other events inherits. Also responsible for registering 
 * and notice eventlisteners about events.
 * 
 */
class Event {

	/**
	 *
	 * @var array
	 */
	protected static $events = array();

	/**
	 *
	 * @var bool
	 */
	protected static $fire_test_events = false;

	/**
	 * Start running the event system, even if we are running tests
	 * 
	 * @param bool $fire 
	 */
	public static function trigger_events_during_testing($boolean) {
		self::$fire_test_events = (bool)$boolean;
	}

	/**
	 * Example usage: Event::register_event('UpdateEvent', UpdateEventListener')
	 * 
	 * @param string $eventClass
	 * @param string $listenerMarker 
	 */
	public static function register_event($eventClass, $listenerMarker) {

		if (!ClassInfo::exists($eventClass)) {
			throw new Exception('Can\'t find class "'.$eventClass.'" for event handling.');
		}

		if (!ClassInfo::exists($listenerMarker)) {
			throw new Exception('Can\'t find interface "'.$listenerMarker.'" for event handling.');
		}

		$implementors = ClassInfo::implementorsOf($listenerMarker);

		if (!$implementors) {
			throw new Exception('Can\'t find implementators of "'.$listenerMarker.'"');
		}
		self::$events[$eventClass] = $listenerMarker;
	}

	/**
	 *
	 * @param Event $eventClass
	 * @return bool - if the event was fired or not
	 * @throws Exception 
	 */
	public static function fire_event(Event $eventClass) {
		if (class_exists('SapphireTest') && SapphireTest::is_running_test() && !self::$fire_test_events) {
			return false;
		}

		$eventClassName = get_class($eventClass);

		if (!array_key_exists($eventClassName, self::$events)) {
			throw new Exception('The event "'.$eventClassName.'" has not yet been registered and therefore can\'t be triggered.');
		}

		$interfaceName = self::$events[$eventClassName];

		$implementors = ClassInfo::implementorsOf($interfaceName);

		$methodName = lcfirst(str_replace('Listener', '', $interfaceName));

		foreach ($implementors as $implementor) {
			$controller = new $implementor;
			$controller->$methodName($eventClass);
		}

		return true;
	}

	/**
	 * Remove all previous registered events and listeners
	 * 
	 */
	public static function clear() {
		self::$events = array();
	}

}
