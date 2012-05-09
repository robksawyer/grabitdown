<?php
App::uses('ErrorHandler', 'Error');
App::uses('CakeRequest', 'Network');

class MyErrorHandler extends ErrorHandler {
	
	/**
	 * override core one with the following enhancements/fixes:
	 * - 404s log to a different domain
	 * - IP, Referer and Browser-Infos are added for better error debugging/tracing
	 * 2011-12-21 ms
	 */
	public static function handleException(Exception $exception) {
		$config = Configure::read('Exception');
		
		if (!empty($config['log'])) {
			$log = LOG_ERR;	
			$message = sprintf("[%s] %s\n%s\n%s",
				get_class($exception),
				$exception->getMessage(),
				$exception->getTraceAsString(),
				self::traceDetails()
			);
			if (in_array(get_class($exception), array('MissingControllerException', 'MissingActionException', 'PrivateActionException', 'NotFoundException'))) {
				$log = '404';
			}
			CakeLog::write($log, $message);
		}
		$renderer = $config['renderer'];
		if ($renderer !== 'ExceptionRenderer') {
			list($plugin, $renderer) = pluginSplit($renderer, true);
			App::uses($renderer, $plugin . 'Error');
		}
		try {
			$error = new $renderer($exception);
			$error->render();
		} catch (Exception $e) {
			set_error_handler(Configure::read('Error.handler')); // Should be using configured ErrorHandler
			Configure::write('Error.trace', false); // trace is useless here since it's internal
			$message = sprintf("[%s] %s\n%s\n%s", // Keeping same message format
				get_class($e),
				$e->getMessage(),
				$e->getTraceAsString(),
				self::traceDetails()
			);
			trigger_error($message, E_USER_ERROR);
		}
	}
	
	/**
	 * override core one with the following enhancements/fixes:
	 * - 404s log to a different domain
	 * - IP, Referer and Browser-Infos are added for better error debugging/tracing
	 * 2011-12-21 ms
	 */
	public static function handleError($code, $description, $file = null, $line = null, $context = null) {
		if (error_reporting() === 0) {
			return false;
		}
		$errorConfig = Configure::read('Error');
		list($error, $log) = self::mapErrorCode($code);

		$debug = Configure::read('debug');
		if ($debug) {
			$data = array(
				'level' => $log,
				'code' => $code,
				'error' => $error,
				'description' => $description,
				'file' => $file,
				'line' => $line,
				'context' => $context,
				'start' => 2,
				'path' => Debugger::trimPath($file)
			);
			return Debugger::getInstance()->outputError($data);
		} else {
			$message = $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
			if (!empty($errorConfig['trace'])) {
				$trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
				$message .= "\nTrace:\n" . $trace . "\n";
				$message .= self::traceDetails();
			}
			return CakeLog::write($log, $message);
		}
	}
	
	/**
	 * append some more infos to better track down the error
	 * @return string
	 * 2011-12-21 ms
	 */
	public static function traceDetails() {
		App::uses('CommonComponent', 'Tools.Controller/Component');
		$currentUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'n/a';
		$refererUrl = CommonComponent::getReferer(); //Router::getRequest()->url().'
		App::uses('CakeSession', 'Model/Datasource');  
		$uid = CakeSession::read('Auth.User.id');
		if (empty($uid)) {
			$uid = (!empty($_SESSION) && !empty($_SESSION['Auth']['User']['id'])) ? $_SESSION['Auth']['User']['id'] : null;
		}
		
		$data = array(
			@CakeRequest::clientIp(),
			$currentUrl.(!empty($refererUrl) ? (' ('.$refererUrl.')') : ''), 
			$uid,
			env('HTTP_USER_AGENT')
		);
		return implode(' - ', $data);
	}
	
}