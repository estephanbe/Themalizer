<?php

/**
 * Tests.php
 *
 * @package BoshDev
 */

namespace Themalizer\Helper;

/**
 * Contain testing methods
 */
trait Tests
{

	/**
	 * Use var_dump php function to dump any $var and stop excuting the code.
	 *
	 * @param mixed $var the dumped variable.
	 * @return void
	 */
	public static function dump_this($var)
	{
		var_dump($var); // phpcs:ignore
		die;
	}

	/**
	 * Check if the value existes or not and return an exception message if it is not existing.
	 *
	 * @param mixed  $var testing variable.
	 * @param string $msg the output message.
	 * @return Exception
	 * @throws \Exception Variable message.
	 */
	public static function isset_test($var, $msg)
	{
		try {
			if (!isset($var)) {
				throw new \Exception($msg);
			}
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Check if the value empty or not and return an exception message if it is empty.
	 *
	 * @param mixed  $var testing variable.
	 * @param string $msg the output message.
	 * @return Exception
	 * @throws \Exception Variable message.
	 */
	public static function empty_test($var, $msg)
	{
		try {
			if (empty($var)) {
				throw new \Exception($msg);
			}
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Check if the value empty or not, existed or not and return an exception message if it is not existed and empty.
	 *
	 * @param mixed  $var testing variable.
	 * @param string $msg the output message.
	 * @return void as it depends on the methods returns.
	 * @throws \Exception Variable message.
	 */
	public static function empty_isset_test($var, $msg)
	{
		self::isset_test($var, $msg);
		self::empty_test($var, $msg);
	}

	/**
	 * Check if the value is instence of BoshDev\Core\Init or not and return an exception message if it is not.
	 *
	 * @param object $obj testing object.
	 * @param string $msg the output message.
	 * @return Exception
	 * @throws \Exception Please make sure the object is instance of INIT class.
	 */
	public static function is_init_test($obj, $msg = '')
	{
		$message = empty($msg) ? 'Please make sure the object is instance of INIT class.' : $msg;
		try {
			if (!$obj instanceof \BoshDev\Core\Init) {
				throw new \Exception($message);
			}
		} catch (\Exception $e) {
			return $e->getMassege();
		}
	}

	/**
	 * Check if the value is instence of the testing class or not and return an exception message if it is not.
	 *
	 * @param object $obj testing object.
	 * @param string $class testing class.
	 * @return Exception
	 * @throws \Exception "Please make sure that the object is an instance of $class".
	 */
	public static function is_instanceof_test($obj, $class)
	{
		try {
			if (!$obj instanceof $class) {
				throw new \Exception("Please make sure that the object is an instance of $class");
			}
		} catch (\Exception $e) {
			return $e->getMassege();
		}
	}

	/**
	 * Check if the value of the variable is equal to the given string and return an exception message if not.
	 *
	 * @param mixed  $var testing variable.
	 * @param string $str the given string.
	 * @return Exception
	 * @throws \Exception 'Please make sure the variable value is equal to "' . $str . '"'.
	 */
	public static function string_test($var, $str)
	{
		try {
			if ($var !== $str) {
				throw new \Exception('Please make sure the variable value is equal to "' . $str . '"');
			}
		} catch (\Exception $e) {
			return $e->getMassege();
		}
	}

	public static function existed_url_test(string $url)
	{
		$file_headers = @get_headers($url);
		if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
			$exists = false;
		} else {
			$exists = true;
		}
		return $exists;
	}
}
