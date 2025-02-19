<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\StateMachine;
use TheMikkel\Assignment1\MachineInterpreter;
use PHPUnit\Framework\TestCase;

class CDPlayerTest extends TestCase {
	
	private MachineInterpreter $interpreter;
	
	protected function setUp(): void {
		$stateMachine = new StateMachine();
		$NUMBER_TRACKS = 10;
		$m = $stateMachine
					->integer("track")
					->state("STOP")->initial()
						->when("PLAY")->to("PLAYING")->set("track", 1)->ifEquals("track", 0)
						->when("PLAY")->to("PLAYING")
					->state("PLAYING")
						->when("STOP")->to("STOP")
						->when("PAUSE")->to("PAUSED")
						->when("TRACK_END")->to("STOP")->ifEquals("track", $NUMBER_TRACKS)
						->when("TRACK_END")->to("PLAYING")->increment("track")
					->state("PAUSED")
						->when("STOP")->to("STOP")
						->when("PLAY")->to("PLAYING")
						->when("FORWARD")->to("PAUSED")->increment("track")->ifLessThan("track", $NUMBER_TRACKS + 1)
						->when("BACK")->to("PAUSED")->decrement("track")->ifGreaterThan("track", 1)
					->build();
		$this->interpreter = new MachineInterpreter();
		$this->interpreter->run($m);
	}
	
	public function testPlayMusic() {
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
