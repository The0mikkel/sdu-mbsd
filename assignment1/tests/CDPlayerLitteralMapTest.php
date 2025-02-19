<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\Builder\Decrement;
use TheMikkel\Assignment1\Builder\IfEquals;
use TheMikkel\Assignment1\Builder\IfGreaterThan;
use TheMikkel\Assignment1\Builder\IfLessThan;
use TheMikkel\Assignment1\Builder\Increment;
use TheMikkel\Assignment1\Builder\Set;
use TheMikkel\Assignment1\Builder\Transition;
use TheMikkel\Assignment1\StateMachine;
use TheMikkel\Assignment1\MachineInterpreter;
use PHPUnit\Framework\TestCase;

class CDPlayerLitteralMapTest extends TestCase
{
	private MachineInterpreter $interpreter;

	protected function setUp(): void
	{
		$stateMachine = new StateMachine();
		$NUMBER_TRACKS = 10;
		$m = $stateMachine
			->integer("track")
			->state(
				name: "STOP",
				initial: true,
				transitions: [
					new Transition(
						when: "PLAY",
						to: "PLAYING",
						set: new Set("track", 1),
						ifEquals: new IfEquals("track", 0),
					),
					new Transition(
						when: "PLAY",
						to: "PLAYING"
					)
				],
			)
			->state(
				name: "PLAYING",
				transitions: [
					new Transition(
						when: "STOP",
						to: "STOP"
					),
					new Transition(
						when: "PAUSE",
						to: "PAUSED"
					),
					new Transition(
						when: "TRACK_END",
						to: "STOP",
						ifEquals: new IfEquals("track", $NUMBER_TRACKS)
					),
					new Transition(
						when: "TRACK_END",
						to: "PLAYING",
						Increment: new Increment("track"),
					)
				]
			)
			->state("PAUSED", [
				new Transition("STOP", "STOP"),
				new Transition("PLAY", "PLAYING"),
				new Transition("FORWARD", "PAUSED", new Increment("track"), new IfLessThan("track", $NUMBER_TRACKS + 1)),
				new Transition("BACK", "PAUSED", new Decrement("track"), new IfGreaterThan("track", 1))
			])
			->build();
		$this->interpreter = new MachineInterpreter();
		$this->interpreter->run($m);
	}

	public function testPlayMusic()
	{
		$this->interpreter->processEvent("PLAY");
		$this->assertEquals(1, $this->interpreter->getInteger("track"));
		$this->assertEquals("PLAYING", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("TRACK_END");
		$this->assertEquals(2, $this->interpreter->getInteger("track"));
		$this->assertEquals("PLAYING", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("STOP");
		$this->assertEquals(2, $this->interpreter->getInteger("track"));
		$this->assertEquals("STOP", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("PLAY");
		$this->assertEquals(2, $this->interpreter->getInteger("track"));
		$this->assertEquals("PLAYING", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("PAUSE");
		$this->assertEquals(2, $this->interpreter->getInteger("track"));
		$this->assertEquals("PAUSED", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("BACK");
		$this->assertEquals(1, $this->interpreter->getInteger("track"));
		$this->assertEquals("PAUSED", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("FORWARD");
		$this->assertEquals(2, $this->interpreter->getInteger("track"));
		$this->assertEquals("PAUSED", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->interpreter->processEvent("FORWARD");
		$this->assertEquals(10, $this->interpreter->getInteger("track"));
		$this->assertEquals("PAUSED", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("PLAY");
		$this->assertEquals(10, $this->interpreter->getInteger("track"));
		$this->assertEquals("PLAYING", $this->interpreter->getCurrentState()->getName());

		$this->interpreter->processEvent("TRACK_END");
		$this->assertEquals(10, $this->interpreter->getInteger("track"));
		$this->assertEquals("STOP", $this->interpreter->getCurrentState()->getName());
	}
}
