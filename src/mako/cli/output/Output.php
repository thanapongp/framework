<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\cli\output;

use mako\cli\output\formatter\FormatterInterface;
use mako\cli\output\writer\WriterInterface;

/**
 * Output.
 *
 * @author Frederic G. Østby
 */
class Output
{
	/**
	 * Standard output.
	 *
	 * @var int
	 */
	const STANDARD = 1;

	/**
	 * Error output
	 *
	 * @var int
	 */
	const ERROR = 2;

	/**
	 * Standard writer.
	 *
	 * @var \mako\cli\output\writer\WriterInterface
	 */
	protected $standard;

	/**
	 * Error writer.
	 *
	 * @var \mako\cli\output\writer\WriterInterface
	 */
	protected $error;

	/**
	 * Formatter
	 *
	 * @var \mako\cli\output\formatter\FormatterInterface|null
	 */
	protected $formatter;

	/**
	 * Is the output muted?
	 *
	 * @var bool
	 */
	 protected $muted = false;

	/**
	 * Constructor.
	 *
	 * @param \mako\cli\output\writer\WriterInterface            $standard  Standard writer
	 * @param \mako\cli\output\writer\WriterInterface            $error     Error writer
	 * @param null|\mako\cli\output\formatter\FormatterInterface $formatter Formatter
	 */
	public function __construct(WriterInterface $standard, WriterInterface $error, FormatterInterface $formatter = null)
	{
		$this->standard = $standard;

		$this->error = $error;

		$this->formatter = $formatter;
	}

	/**
	 * Sets the formatter.
	 *
	 * @param \mako\cli\output\formatter\FormatterInterface $formatter Formatter
	 */
	public function setFormatter(FormatterInterface $formatter)
	{
		$this->formatter = $formatter;
	}

	/**
	 * Returns the formatter.
	 *
	 * @return \mako\cli\output\formatter\FormatterInterface|null
	 */
	public function getFormatter()
	{
		return $this->formatter;
	}

	/**
	 * Mutes the output.
	 */
	public function mute()
	{
		$this->muted = true;
	}

	/**
	 * Unmutes the output.
	 */
	public function unmute()
	{
		$this->muted = false;
	}

	/**
	 * Is the output muted?
	 *
	 * @return bool
	 */
	public function isMuted(): bool
	{
		return $this->muted;
	}

	/**
	 * Writes string to output.
	 *
	 * @param string $string String to write
	 * @param int    $writer Output type
	 */
	public function write(string $string, int $writer = Output::STANDARD)
	{
		if($this->muted)
		{
			return;
		}

		$writer = ($writer === static::STANDARD) ? $this->standard : $this->error;

		if($this->formatter !== null)
		{
			if($writer->isDirect())
			{
				$string = $this->formatter->format($string);
			}
			else
			{
				$string = $this->formatter->strip($string);
			}
		}

		$writer->write($string);
	}

	/**
	 * Writes string to output using the error writer.
	 *
	 * @param string $string String to write
	 */
	public function error(string $string)
	{
		$this->write($string, static::ERROR);
	}

	/**
	 * Appends newline to string and writes it to output.
	 *
	 * @param string $string String to write
	 * @param int    $writer Output type
	 */
	public function writeLn(string $string, int $writer = Output::STANDARD)
	{
		$this->write($string . PHP_EOL, $writer);
	}

	/**
	 * Appends newline to string and writes it to output using the error writer.
	 *
	 * @param string $string String to write
	 */
	public function errorLn(string $string)
	{
		$this->writeLn($string, static::ERROR);
	}

	/**
	 * Clears the screen.
	 */
	public function clear()
	{
		$this->write("\e[H\e[2J");
	}
}
