<?php

namespace Lucinda\MVC;

use Lucinda\MVC\EventListener\Faceted;
use Lucinda\MVC\EventListener\MultiFaceted;
use Lucinda\MVC\Response\Transformer\Transformer;

/**
 * Schedules events (but doesn't run them)
 */
final class EventScheduler
{
    private array $events = [];

    /**
     * Schedules an event
     * 
     * @param EventType $eventType
     * @param string $className
     * @return void
     * @throws ConfigurationException If event doesn't pass validation checks
     */
    public function add(EventType $eventType, string $className): void
    {
        $message = $this->validate($eventType, $className);
        if ($message !== null) {
            throw new ConfigurationException($message);
        }
        $this->events[$eventType->value][$className] = $className;
    }

    /**
     * Validates event before adding
     * 
     * @param EventType $eventType
     * @param string $className
     * @return ?string Error message if any
     */
    private function validate(EventType $eventType, string $className): ?string
    {
        if (!class_exists($className)) {
            return "Class must exist: ".$className;
        }

        if (!is_subclass_of($className, EventListener::class)) {
            return "Class must be a subclass of: ".EventListener::class;
        }

        if (
            ($eventType == EventType::RESPONSE || $eventType == EventType::END) && 
            (is_subclass_of($className, Faceted::class) || is_subclass_of($className, MultiFaceted::class))
        ) {
            return "Response/End event listeners must not be faceted";
        }

        if ($eventType == EventType::RESPONSE && !is_subclass_of($className, Transformer::class)) {
            return "Response event listeners must implement a ".Transformer::class;
        }

        if (isset($this->events[$eventType->value][$className])) {
            return "Event already registered: ".$className;
        }

        return null;
    }

    /**
     * Gets event listeners scheduled
     * 
     * @param EventType $eventType
     * @return string[]
     */
    public function get(EventType $eventType): array
    {
        return $this->events[$eventType->value]??[];
    }
}