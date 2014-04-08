<?php
App::uses('ErrorHandler', 'Error');

class ApiError extends ErrorHandler {
    public static function handleError($code, $description, $file = null,
        $line = null, $context = null) {
        self::error(__('There is an error occurred.'));

        list($error, $log) = self::mapErrorCode($code);
        $message = $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
        if (!empty($errorConfig['trace'])) {
            $trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
            $message .= "\nTrace:\n" . $trace . "\n";
        }
        return CakeLog::write($log, $message);
    }

    public static function handleException($error) {
        self::error(__('Bad request.'));

        $config = Configure::read('Exception');
        self::_log($exception, $config);
    }

    protected static function error($message = NULL){
        header('Content-type: text/json; charset=utf-8');
        header('Status: 400 Bad Request');

        echo json_encode(array(
            'error' => true,
            'message' => $message
        ));

        exit;
    }
}