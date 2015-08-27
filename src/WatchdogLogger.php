<?php

namespace Drupal\Log;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;


/**
 * Provides a Drupal watchdog logger.
 *
 */
class WatchdogLogger extends AbstractLogger
{
    /**
     * The minimum level of logging to ignore. All events at or below this level
     * will be ignored. This is a LogLevel constant.
     *
     * @var string
     *
     */
    protected $ignore = null;


    /**
     * Constructor
     *
     * @param string $ignore_level
     *   The minimum level of logging to ignore. All events at or below this
     *   level will be ignored.
     *
     */
    public function __construct($ignore_level = null)
    {
        $this->ignore = $ignore_level;
    }


    /**
     * {@inheritdoc}
     *
     */
    public function log($level, $message, array $context = array())
    {
        $map = array(
              LogLevel::EMERGENCY => WATCHDOG_EMERGENCY,
              LogLevel::ALERT     => WATCHDOG_ALERT,
              LogLevel::CRITICAL  => WATCHDOG_CRITICAL,
              LogLevel::ERROR     => WATCHDOG_ERROR,
              LogLevel::WARNING   => WATCHDOG_WARNING,
              LogLevel::NOTICE    => WATCHDOG_NOTICE,
              LogLevel::INFO      => WATCHDOG_INFO,
              LogLevel::DEBUG     => WATCHDOG_DEBUG,
        );

        $ignore   = isset($map[$this->ignore]) ? $map[$this->ignore] : null;
        $severity = isset($map[$level]) ? $map[$level] : WATCHDOG_NOTICE;

        if (is_int($ignore) && $severity >= $ignore) {
          return;
        }

        // This is pretty hacky. Basically, we want to know what called the log
        $trace    = debug_backtrace();
        $index    = isset($trace[1]['class']) && $trace[1]['class'] == 'Psr\Log\AbstractLogger' ? 2 : 1;
        $facility = $trace[$index]['function'];

        if (!empty($trace[$index]['class'])) {
            $facility = $trace[$index]['class'] . '::' . $facility;
        }

        watchdog($facility, $message, $context, $severity);
    }

}