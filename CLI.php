<?php
/**
 * Copyright by Timon Kreis - All Rights Reserved
 * Visit https://www.wildbunny.de
 */
declare(strict_types = 1);

namespace WildBunny\Framework\CLI;

use WildBunny\Framework;

/**
 * @author Timon Kreis <info@wildbunny.de>
 * @category WildBunny.Framework
 * @package cli
 */
class CLI
{
	// Vordergrundfarben
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

	// Hintergrundfarben
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
	 * @throws \Exception
	 */
	public function __construct()
	{
		if (PHP_SAPI != 'cli') {
			throw new \Exception('PHP not running in CLI mode');
		}
	}

	/**
	 * Liefert die aktuelle Farbe
	 *
	 * @return int|null
	 */
	public function getColor() : ?int
	{
		return $this->color;
	}

	/**
	 * Setzt die Farbe
	 *
	 * @param int|null $color
	 * @return $this
	 */
	public function setColor(int $color = null) : CLI
	{
		if ($this->color !== $color) {
			$this->color = $color;

			$textColors = [
				1 => '0;30', // Schwarz
				2 => '1;30', // Dunkelgrau
				4 => '0;31', // Rot
				8 => '1;31', // Hellrot
				16 => '0;32', // Grün
				32 => '1;32', // Hellgrün
				64 => '0;33', // Braun
				128 => '1;33', // Gelb
				256 => '0;34', // Blau
				512 => '1;34', // Hellblau
				1024 => '0;35', // Violett
				2048 => '1;35', // Hellviolett
				4096 => '0;36', // Türkis
				8192 => '1;36', // Helltürkis
				16384 => '0;37', // Hellgrau
				32768 => '1;37' // Weiß
			];

			$bgColors = [
				65536 => 40, // Schwarz
				131072 => 41, // Rot
				262144 => 42, // Grün
				524288 => 43, // Gelb
				1048576 => 44, // Blau
				2097152 => 45, // Pink
				4194304 => 46, // Türkis
				8388608 => 47 // Hellgrau
			];

			if ($color) {
				foreach ($textColors as $index => $textColor) {
					if ($color & $index) {
						echo "\033[" . $textColor . 'm';

						break;
					}
				}

				foreach ($bgColors as $index => $bgColor) {
					if ($color & $index) {
						echo "\033[" . $bgColor . 'm';

						break;
					}
				}
			}
			else {
				echo "\033[0m";
			}
		}

		return $this;
	}

	/**
	 * Setzt die Ausgabe zurück
	 *
	 * @return $this
	 */
	public function clear() : CLI
	{
		echo str_repeat(PHP_EOL, 100);

		return $this;
	}

	/**
	 * Liefert eine Ausgabe
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

		echo $message;

		if ($color) {
			$this->setColor($prevColor);
		}

		if ($newLine) {
			echo PHP_EOL;
		}

		return $this;
	}

	/**
	 * Wartet auf eine Eingabe des Benutzers
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
	 * Pausiert das Skript bis zum Drücken der Enter-Taste
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
