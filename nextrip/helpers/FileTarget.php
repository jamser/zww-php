<?php
namespace nextrip\helpers;

use yii\helpers\ArrayHelper;
use yii\log\Logger;

class FileTarget extends \yii\log\FileTarget {
    
    /**
     * Generates the context information to be logged.
     * The default implementation will dump user information, system variables, etc.
     * @return string the context information. If an empty string, it means no context information.
     */
    protected function getContextMessage()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        return json_encode($context, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Formats a log message for display as a string.
     * @param array $message the log message to be formatted.
     * The message structure follows that in [[Logger::messages]].
     * @return string the formatted message
     */
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = json_encode($text, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $text = json_encode($text, JSON_UNESCAPED_UNICODE);
        }
        
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $prefix = $this->getMessagePrefix($message);
        return date('Y-m-d H:i:s', $timestamp) . " {$prefix}[$level][$category] $text "
            . (empty($traces) ? '' : json_encode($traces, JSON_UNESCAPED_UNICODE));
    }
    
}
