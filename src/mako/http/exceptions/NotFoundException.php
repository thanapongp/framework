<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\exceptions;

use Throwable;

use mako\http\exceptions\RequestException;

/**
 * Not found exception.
 *
 * @author Frederic G. Østby
 */
class NotFoundException extends RequestException
{
	/**
	 * Constructor.
	 *
	 * @param null|string     $message  Exception message
	 * @param null|\Throwable $previous Previous exception
	 */
	public function __construct(string $message = null, Throwable $previous = null)
	{
		parent::__construct(404, $message, $previous);
	}
}
