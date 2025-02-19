<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\StateMachine;
use PHPUnit\Framework\TestCase;

class MachineStructureTest extends TestCase {
	private StateMachine $stateMachine;
	
	protected function setUp(): void {
		$this->stateMachine = new StateMachine();
	}
	
	public function testEmptyMachine() {
		$m = $this->stateMachine->build();
		$this->assertTrue(empty($m->getStates()));
	}
	
	public function testStates() {
		$m = $this->stateMachine
						->state("state 1")
						->state("state 2")
						->state("state 3")
						->build();
		$states = $m->getStates();
		$this->assertEquals(3, count($m->getStates()));
		$this->assertEquals(1, count(array_filter($states, fn($state) => $state->getName() === "state 1")));
		$this->assertEquals(1, count(array_filter($states, fn($state) => $state->getName() === "state 2")));
		$this->assertEquals(1, count(array_filter($states, fn($state) => $state->getName() === "state 3")));
	}
	
	public function testInitialFirstState() {
		$m = $this->stateMachine
				->state("state 1")->initial()
				->state("state 2")
				->state("state 3")
				->build();
		$this->assertEquals("state 1", $m->getInitialState()->getName());
	}
	
	public function testInitialState() {
		$m = $this->stateMachine
				->state("state 1")
				->state("state 2")->initial()
				->state("state 3")
				->build();
		$this->assertEquals("state 2", $m->getInitialState()->getName());		
	}
	
	public function testGetState() {
		$m = $this->stateMachine
				->state("state 1")
				->state("state 2")->initial()
				->state("state 3")
				->build();
		$this->assertEquals("state 2", $m->getState("state 2")->getName());
	}
	
	public function testNoTransitions() {
		$m = $this->stateMachine
				->state("state 1")
				->build();
		$state = $m->getState("state 1");
		$transitions = $state->getTransitions();
		$this->assertTrue(empty($transitions));
	}
	
	public function testTransitions() {
		$m = $this->stateMachine
					->state("state 1")
						->when("change to 2")->to("state 2")
						->when("change to 3")->to("state 3")
					->state("state 2")
						->when("change to 3")->to("state 3")
					->state("state 3")
					->build();
		$state = $m->getState("state 1");
		$transitions = $state->getTransitions();
		$this->assertEquals(2, count($transitions));
		$this->assertEquals(1, count(array_filter($transitions, fn($transition) => $transition->getEvent() === "change to 2")));
		$this->assertEquals("state 2", $state->getTransitionByEvent("change to 2")[0]->getTarget()->getName());
		$this->assertEquals(1, count(array_filter($transitions, fn($transition) => $transition->getEvent() === "change to 3")));
		$this->assertEquals("state 3", $state->getTransitionByEvent("change to 3")[0]->getTarget()->getName());
		
		$state = $m->getState("state 2");
		$transitions = $state->getTransitions();
		$this->assertEquals(1, count($transitions));
		$this->assertEquals(1, count(array_filter($transitions, fn($transition) => $transition->getEvent() === "change to 3")));
		$this->assertEquals("state 3", $state->getTransitionByEvent("change to 3")[0]->getTarget()->getName());
	}
	
	public function testNoVariables() {
		$m = $this->stateMachine->build();
		$this->assertEquals(0, $m->numberOfIntegers());
	}
	
	public function testAddVariable() {
		$m = $this->stateMachine
					->integer("var")
					->build();
		$this->assertEquals(1, $m->numberOfIntegers());
		$this->assertTrue($m->hasInteger("var"));
		$this->assertFalse($m->hasInteger("var 2"));
	}
	
	public function testTransitionSetVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("SET")->to("state 2")->set("var", 42)
					->state("state 2")
					->build();
		$transition = $m->getState("state 1")->getTransitions()[0];
		$this->assertTrue($transition->hasSetOperation());
		$this->assertFalse($transition->hasIncrementOperation());
		$this->assertFalse($transition->hasDecrementOperation());
		$this->assertEquals("var", $transition->getOperationVariableName());
	}
	
	public function testTransitionIncrementVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("SET")->to("state 2")->increment("var")
					->state("state 2")
					->build();
		$transition = $m->getState("state 1")->getTransitions()[0];
		$this->assertFalse($transition->hasSetOperation());
		$this->assertTrue($transition->hasIncrementOperation());
		$this->assertFalse($transition->hasDecrementOperation());
		$this->assertEquals("var", $transition->getOperationVariableName());
	}
	
	public function testTransitionDecrementVariable() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("SET")->to("state 2")->decrement("var")
					->state("state 2")
					->build();
		$transition = $m->getState("state 1")->getTransitions()[0];
		$this->assertFalse($transition->hasSetOperation());
		$this->assertFalse($transition->hasIncrementOperation());
		$this->assertTrue($transition->hasDecrementOperation());
		$this->assertEquals("var", $transition->getOperationVariableName());
	}

	public function testTransitionIfVariableEqual() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->ifEquals("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
        $this->assertTrue($transition->isConditional());
        $this->assertEquals("var", $transition->getConditionVariableName());
        $this->assertEquals(42, $transition->getConditionComparedValue());
        $this->assertTrue($transition->isConditionEqual());
        $this->assertFalse($transition->isConditionGreaterThan());
        $this->assertFalse($transition->isConditionLessThan());
	}
	
	public function testTransitionIfVariableGreaterThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->ifGreaterThan("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
	    $this->assertTrue($transition->isConditional());
	    $this->assertEquals("var", $transition->getConditionVariableName());
	    $this->assertEquals(42, $transition->getConditionComparedValue());
	    $this->assertFalse($transition->isConditionEqual());
	    $this->assertTrue($transition->isConditionGreaterThan());
	    $this->assertFalse($transition->isConditionLessThan());
	}
	
	public function testTransitionIfVariableLessThan() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->ifLessThan("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
        $this->assertTrue($transition->isConditional());
        $this->assertEquals("var", $transition->getConditionVariableName());
        $this->assertEquals(42, $transition->getConditionComparedValue());
        $this->assertFalse($transition->isConditionEqual());
        $this->assertFalse($transition->isConditionGreaterThan());
        $this->assertTrue($transition->isConditionLessThan());
	}

	public function testTransitionIfVariableEqualsAndSet() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->set("var", 10)->ifEquals("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
		$this->assertTrue($transition->isConditional());
		$this->assertTrue($transition->hasSetOperation());
	}
	
	public function testTransitionIfVariableGreaterAndIncrement() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->increment("var")->ifGreaterThan("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
		$this->assertTrue($transition->isConditional());
		$this->assertTrue($transition->hasOperation());
	}

	public function testTransitionIfVariableLessAndDecrement() {
		$m = $this->stateMachine
					->integer("var")
					->state("state 1")
						->when("GO")->to("state 2")->decrement("var")->ifLessThan("var", 42)
					->state("state 2")
					->build();
		$state = $m->getState("state 1");
		$transition = $state->getTransitions()[0];
		$this->assertTrue($transition->isConditional());
		$this->assertTrue($transition->hasOperation());
	}
}
