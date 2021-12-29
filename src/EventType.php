<?php
namespace Lucinda\ConsoleSTDOUT;

/**
 * Enum of events supported by API for whom listeners can be attached
 */
enum EventType: string
{
    case START = "start";
    case APPLICATION = "application";
    case REQUEST = "request";
    case RESPONSE = "response";
    case END = "end";
}
