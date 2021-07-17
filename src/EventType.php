<?php
namespace Lucinda\ConsoleSTDOUT;

/**
 * Enum of events supported by API for whom listeners can be attached
 */
class EventType
{
    const START = "start";
    const APPLICATION = "application";
    const REQUEST = "request";
    const RESPONSE = "response";
    const END = "end";
}
