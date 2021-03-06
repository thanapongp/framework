<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\exceptions;

use Throwable;

use mako\http\exceptions\RequestException;

/**
 * Method not allowed exception.
 *
 * @author Frederic G. Østby
 */
class MethodNotAllowedException extends RequestException
{
	/**
	 * Allowed methods.
	 *
	 * @var array
	 */
	 protected $allowedMethods;

	/**
	 * Constructor.
	 *
	 * @param array           $allowedMethods Allowed methods
	 * @param null|string     $message        Exception message
	 * @param null|\Throwable $previous       Previous exception
	 */
	public function __construct(array $allowedMethods = [], string $message = null, Throwable $previous = null)
	{
		$this->allowedMethods = $allowedMethods;

		parent::__construct(405, $message, $previous);
	}

	/**
	 * Returns the allowed methods.
	 *
	 * @return array
	 */
	public function getAllowedMethods()
	{
		return $this->allowedMethods;
	}
}
