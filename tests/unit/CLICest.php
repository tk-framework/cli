<?php
/**
 * Copyright by Timon Kreis - All Rights Reserved
 * Visit https://www.timonkreis.de
 */
declare(strict_types = 1);

use TimonKreis\Framework;

/**
 * @category tk-framework
 * @package cli
 */
class CLICest
{
	/**
	 * @var Framework\CLI\CLI
	 */
	protected $cli;

	/**
	 * @var string
	 */
	protected $streamFile;

	/**
	 * @var resource
	 */
	protected $stream;

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function _before(Framework\Test\UnitTester $I)
	{
		$this->cli = new Framework\CLI\CLI();
		$this->streamFile = dirname(__DIR__) . '/_output/resource';
		$this->stream = fopen($this->streamFile, 'w');
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function _after(Framework\Test\UnitTester $I)
	{
		@fclose($this->stream);
		@unlink($this->streamFile);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testColorGetterAndSetter(Framework\Test\UnitTester $I) : void
	{
		$I->assertNull($this->cli->getColor());

		$this->cli->setColor($this->cli::TEXT_YELLOW);

		$I->assertEquals($this->cli->getColor(), $this->cli::TEXT_YELLOW);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testStreamGetterAndSetter(Framework\Test\UnitTester $I) : void
	{
		$I->assertEquals($this->cli->getStream(), STDOUT);

		$this->cli->setStream(STDERR);

		$I->assertEquals($this->cli->getStream(), STDERR);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testClearMethod(Framework\Test\UnitTester $I) : void
	{
		$this->cli->setStream($this->stream);
		$this->cli->clear();

		$actual = @file_get_contents($this->streamFile);
		$expected = str_repeat(PHP_EOL, 100);

		$I->assertEquals($actual, $expected);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testEchoMethod(Framework\Test\UnitTester $I) : void
	{
		$this->cli->setStream($this->stream);
		$this->cli->echo('echo-test');
		$this->cli->echo('echo-test', false);

		$actual = @file_get_contents($this->streamFile);
		$expected = 'echo-test' . PHP_EOL . 'echo-test';

		$I->assertEquals($actual, $expected);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testErrorMethod(Framework\Test\UnitTester $I) : void
	{
		$this->cli->error('error-test', true, $this->stream);
		$this->cli->error('error-test', false, $this->stream);

		$actual = @file_get_contents($this->streamFile);
		$expected = 'error-test' . PHP_EOL . 'error-test';

		$I->assertEquals($actual, $expected);
	}
}
