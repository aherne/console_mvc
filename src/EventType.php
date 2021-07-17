<?php
namespace Lucinda\ConsoleSTDOUT;

/**
 * Enum of events supported by API for whom listeners can be attached
 */
class EventType
{
    public const START = "start";
    public const APPLICATION = "application";
    public const REQUEST = "request";
    public const RESPONSE = "response";
    public const END = "end";
}
