# Eventsystem and the static publisher

## Brief

This is system takes care of the business of collecting URLs that needs to be
rebuild, the same URLs as stale and at last rebuilds them. It does this in away
that is designed to leave minimal impact to the visitor and the administrator.

## The Event system

All core code relevant to this system is located in mysite/code/caching

The system is configured in the _config.php with registrering events with
event listeners.

    Event::register_event('UpdateEvent', 'UpdateEventListener'):

This means that the event UpdateEvent gets triggered, it will notify all
objects that implements the interface UpdateEventListener.

The events most likely triggered in a onAfterWrite or onBeforeDelete. This is
how a class would trigger a UpdateEvent:

    public function onAfterWrite() {
        parent::onAfterWrite();
        Event::fire_event(new UpdateEvent($this));
    }

The UpdateEvent takes an reference to the DataObject that triggers this
event.

The Event system then calls every implementator of the UpdateEventListener
with the method Object#UpdateEvent(Event $event). This gives the
implementator a reference to the original triggerer.

The listener (implementor) takes care of collecting a list of URLs that needs to
be updated in it's control.