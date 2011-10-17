<?php

/**
 * This is a Unittest class for EventTest
 * 
 */
class EventTest extends SapphireTest {
	
	
	static $fixture_file = 'events/tests/EventTest.yml';

	public function setUp() {
		parent::setUp();
		// Events do not triggers during testing unless forced
		Event::trigger_events_during_testing(true);
		Event::register_event('EventTest_UpdateEvent', 'EventTest_UpdateEventListener');
	}
	
	public function testGetInstance() {
		$this->assertTrue(new Event instanceof Event, 'Trying to find an instance of Event');
	}
	
	public function testThatEventIsTriggered() {
		$page = new EventTest_Page(array('URLSegment'=>'eventpage'));
		$page->write();
		$this->assertEquals($page, EventTest_Page_Controller::$changed_page);
	}
}

class EventTest_UpdateEvent extends Event implements TestOnly {
	
	/**
	 *
	 * @var Page
	 */
	protected $page;

	/**
	 *
	 * @param Page $page
	 */
	public function __construct(SiteTree $page) {
		$this->page = $page;
	}

	/**
	 *
	 * @return Page
	 */
	public function getPage() {
		return $this->page;
	}
}

/**
 * 
 */
interface EventTest_UpdateEventListener extends TestOnly {
	
	/**
	 *
	 * @param EventTest_UpdateEvent $event 
	 */
	public function EventTest_UpdateEvent(EventTest_UpdateEvent $event);
}

/**
 * 
 */
class EventTest_Page extends SiteTree implements TestOnly {
	
	public function onAfterWrite() {
		Event::fire_event(new EventTest_UpdateEvent($this));
	}
	
}

/**
 * 
 */
class EventTest_Page_Controller extends Controller implements EventTest_UpdateEventListener {
	
	/**
	 *
	 * @var SiteTree
	 */
	public static $changed_page = null;
	
	/**
	 *
	 * @param EventTest_UpdateEvent $event 
	 */
	public function EventTest_UpdateEvent(EventTest_UpdateEvent $event) {
		self::$changed_page = $event->getPage();
	}
}