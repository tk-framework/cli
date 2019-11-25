<?php
/**
 * Copyright by Timon Kreis - All Rights Reserved
 * Visit https://www.netzhase.de
 */
declare(strict_types = 1);

namespace Netzhase\Framework\CLI;

/**
 * @author Timon Kreis <mail@netzhase.de>
 * @category Netzhase.Framework
 * @package cli
 */
class CLI
{
	// Foreground colors
	public const TEXT_BLACK = 1;
	public const TEXT_DARK_GRAY = 2;
	public const TEXT_RED = 4;
	public const TEXT_LIGHT_RED = 8;
	public const TEXT_GREEN = 16;
	public const TEXT_LIGHT_GREEN = 32;
	public const TEXT_BROWN = 64;
	public const TEXT_YELLOW = 128;
	public const TEXT_BLUE = 256;
	public const TEXT_LIGHT_BLUE = 512;
	public const TEXT_PURPLE = 1024;
	public const TEXT_LIGHT_PURPLE = 2048;
	public const TEXT_CYAN = 4096;
	public const TEXT_LIGHT_CYAN = 8192;
	public const TEXT_LIGHT_GRAY = 16384;
	public const TEXT_WHITE = 32768;

	// Background colors
	public const BG_BLACK = 65536;
	public const BG_RED = 131072;
	public const BG_GREEN = 262144;
	public const BG_YELLOW = 524288;
	public const BG_BLUE = 1048576;
	public const BG_MAGENTA = 2097152;
	public const BG_CYAN = 4194304;
	public const BG_LIGHT_GRAY = 8388608;

	/**
	 * @var int|null
	 */
	protected $color = null;

	/**
	 * Get the current color
	 *
	 * @return int|null
	 */
	public function getColor() : ?int
	{
		return $this->color;
	}

	/**
	 * Set the color
	 *
	 * @param int|null $color
	 * @param bool|resource $stream
	 * @return $this
	 */
	public function setColor(int $color = null, $stream = STDOUT) : CLI
	{
		if ($this->color !== $color) {
			$this->color = $color;

			$textColors = [
				1 => '0;30', // Black
				2 => '1;30', // Dark gray
				4 => '0;31', // Red
				8 => '1;31', // Light red
				16 => '0;32', // Green
				32 => '1;32', // Light green
				64 => '0;33', // Brown
				128 => '1;33', // Yellow
				256 => '0;34', // Blue
				512 => '1;34', // Light blue
				1024 => '0;35', // Purple
				2048 => '1;35', // Light purple
				4096 => '0;36', // Cyan
				8192 => '1;36', // Light cyan
				16384 => '0;37', // Light gray
				32768 => '1;37' // White
			];

			$bgColors = [
				65536 => 40, // Black
				131072 => 41, // Red
				262144 => 42, // Green
				524288 => 43, // Yellow
				1048576 => 44, // Blue
				2097152 => 45, // Magenta
				4194304 => 46, // Cyan
				8388608 => 47 // Light gray
			];

			if ($color) {
				foreach ($textColors as $index => $textColor) {
					if ($color & $index) {
						fwrite($stream, "\e[" . $textColor . 'm');

						break;
					}
				}

				foreach ($bgColors as $index => $bgColor) {
					if ($color & $index) {
						fwrite($stream, "\e[" . $bgColor . 'm');

						break;
					}
				}
			}
			else {
				fwrite($stream, "\e[0m");
			}
		}

		return $this;
	}

	/**
	 * Clear the output
	 *
	 * @return $this
	 */
	public function clear() : CLI
	{
		fwrite(STDOUT, str_repeat(PHP_EOL, 100));

		return $this;
	}

	/**
	 * Send an output to STDOUT
	 *
	 * @param string $message
	 * @param bool $newLine
	 * @param int|null $color
	 * @return $this
	 */
	public function echo(string $message = '', bool $newLine = true, int $color = null) : CLI
	{
		$prevColor = $this->color;

		if ($color) {
			$this->setColor($color);
		}

		fwrite(STDOUT, $message);

		if ($color) {
			$this->setColor($prevColor);
		}

		if ($newLine) {
			fwrite(STDOUT, PHP_EOL);
		}

		return $this;
	}

	/**
	 * Send an output to STDERR
	 *
	 * @param string $message
	 * @param bool $newLine
	 * @return $this
	 */
	public function error(string $message = '', bool $newLine = true) : CLI
	{
		fwrite(STDERR, $message);

		if ($newLine) {
			fwrite(STDERR, PHP_EOL);
		}

		return $this;
	}

	/**
	 * Promt input from user
	 *
	 * @param string|null $message
	 * @param bool $newLine
	 * @param int|null $color
	 * @return string
	 */
	public function prompt(string $message = null, bool $newLine = true, int $color = null) : string
	{
		if ($message !== null) {
			$this->echo($message, $newLine, $color);
		}

		if (PHP_OS == 'WINNT') {
			$input = trim(stream_get_line(STDIN, 1024, PHP_EOL));
		}
		else {
			$input = readline();

			if ($input) {
				readline_add_history($input);
			}
		}

		return $input;
	}

	/**
	 * Pause the script until enter is pressed
	 *
	 * @param string|null $message
	 * @param bool $newLine
	 * @param int|null $color
	 * @return $this
	 */
	public function pause(string $message = null, bool $newLine = false, int $color = null) : CLI
	{
		$this->prompt($message, $newLine, $color);

		return $this;
	}
}
