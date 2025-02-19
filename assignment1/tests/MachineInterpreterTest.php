<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\StateMachine;
use TheMikkel\Assignment1\MachineInterpreter;
use PHPUnit\Framework\TestCase;

class MachineInterpreterTest extends TestCase {
	private StateMachine $stateMachine;
	private MachineInterpreter $interpreter;
	
	protected function setUp(): void {
		$this->stateMachine = new StateMachine();
		$this->interpreter = new MachineInterpreter();
	}
	
	public function testStartInitState() {
		$m = $this->stateMachine
					->state("state 1")->initial()
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->assertEquals("state 1", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testEventNoTransition() {
		$m = $this->stateMachine
				->state("state 1")->initial()
					->when("FIRE")->to("state 2")
				->state("state 2")
				->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("to 2");
		$this->assertEquals("state 1", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testEventTransition() {
		$m = $this->stateMachine
				->state("state 1")->initial()
					->when("FIRE")->to("state 2")
				->state("state 2")
				->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("FIRE");
		$this->assertEquals("state 2", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testListOfEvents() {
		$m = $this->stateMachine
				->state("state 1")->initial()
					->when("ON")->to("state 2")
				->state("state 2")
					->when("GO")->to("state 3")
				->state("state 3")
				->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("ON");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 3", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testChooseTransition() {
		$m = $this->stateMachine
				->state("state 1")->initial()
					->when("FIRE2")->to("state 2")
					->when("FIRE3")->to("state 3")
					->when("FIRE4")->to("state 4")
				->state("state 2")
				->state("state 3")
				->state("state 4")
				->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("FIRE3");
		$this->assertEquals("state 3", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testInitVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
					->build();
		$this->interpreter->run($m);
		$this->assertEquals(0, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionSetVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("SET")->to("state 2")->set("var", 42)
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("SET");
		$this->assertEquals(42, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionIncrementVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("SET")->to("state 2")->increment("var")
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("SET");
		$this->assertEquals(1, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionDecrementVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("SET")->to("state 2")->decrement("var")
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("SET");
		$this->assertEquals(-1, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionIfVariableEqual() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifEquals("var", 42)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 3", $this->interpreter->getCurrentState()->getName());
	}

	public function testTransitionIfVariableNotEqual() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifEquals("var", 40)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 2", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testTransitionIfVariableGreaterThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifGreaterThan("var", 40)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 3", $this->interpreter->getCurrentState()->getName());
	}

	public function testTransitionIfVariableNotGreaterThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifGreaterThan("var", 42)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 2", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testTransitionIfVariableLessThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifLessThan("var", 45)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 3", $this->interpreter->getCurrentState()->getName());
	}

	public function testTransitionIfVariableNotLessThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)
					->state("state 2")
						->when("GO")->to("state 3")->ifLessThan("var", 42)
					->state("state 3")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->interpreter->processEvent("GO");
		$this->assertEquals("state 2", $this->interpreter->getCurrentState()->getName());
	}
	
	public function testTransitionIfVariableEqualsAndSet() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->set("var", 42)->ifEquals("var", 0)
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->assertEquals(42, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionIfVariableGreaterAndIncrement() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->increment("var")->ifGreaterThan("var", -1)
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->assertEquals(1, $this->interpreter->getInteger("var"));
	}

	public function testTransitionIfVariableLessAndDecrement() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->decrement("var")->ifLessThan("var", 1)
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->assertEquals(-1, $this->interpreter->getInteger("var"));
	}
	
	public function testTransitionOrder() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")->initial()
						->when("GO")->to("state 2")->increment("var")
						->when("GO")->to("state 2")->decrement("var")
					->state("state 2")
					->build();
		$this->interpreter->run($m);
		$this->interpreter->processEvent("GO");
		$this->assertEquals(1, $this->interpreter->getInteger("var"));
	}
}
