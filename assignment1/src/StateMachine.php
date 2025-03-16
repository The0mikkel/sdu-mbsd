<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\Metamodel\Machine;
use TheMikkel\Assignment1\Metamodel\State;
use TheMikkel\Assignment1\Metamodel\Transition;
use TheMikkel\Assignment1\Types\Condition;
use TheMikkel\Assignment1\Types\Operation;
use TheMikkel\Assignment1\Builder\Transition as TransitionBuilder;

class StateMachine
{
	private array $integers = [];
	private array $states = [];
	private ?State $initialState;

	private ?State $currentState = null;
	private ?Transition $currentTransition = null;

	private $transitionTargets = [];


	private Machine $machine;

	public function __construct()
	{
		$this->initialState = null;
		$this->machine = new Machine();
	}

	public function build(): Machine
	{
		$machine = new Machine(
			states: $this->states,
			initialState: $this->initialState,
			integers: $this->integers
		);

		foreach ($this->transitionTargets as $value) {
			$target = $value['target'];
			$transition = $value['transition'];
			$transition->setTarget($machine->getState($target));
		}

		return $machine;
	}


	/**
	 * Add state to state machine
	 * 
	 * @param string $name Name of the state
	 * @param mixed $transitions 
	 * @param bool $initial
	 * @return StateMachine
	 */
	public function state(string $name, ?array $transitions = null, bool $initial = false): StateMachine
	{
		if (array_key_exists($name, $this->states)) {
			$this->currentState = $this->states[$name];
		}

		$this->currentState = new State($name);
		$this->states[$name] = $this->currentState;

		if ($transitions) {
			foreach ($transitions as $transition) {
				$generatedTransition = new Transition(
					event: $transition->getName(),
					operation: $transition->getOperation(),
					operationVariable: $transition->getOperationVariable(),
					operationValue: $transition->getOperationValue(),
					condition: $transition->getCondition(),
					conditionVariable: $transition->getConditionVariable(),
					conditionComparedValue: $transition->getConditionValue(),
				);

				$this->transitionTargets[] = ["target" => $transition->getTarget(), "transition" => $generatedTransition];
				$this->currentState->addTransition($generatedTransition);
			}
		}

		if ($initial) {
			$this->initialState = $this->currentState;
		}

		return $this;
	}

	public function initial(): StateMachine
	{
		if ($this->currentState != null) {
			$this->initialState = $this->currentState;
		}

		return $this;
	}

	public function when(string $string): StateMachine
	{
		$this->currentTransition = new Transition($string);
		$this->currentState->addTransition($this->currentTransition);

		return $this;
	}

	public function to(string $string): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->transitionTargets[] = ["target" => $string, "transition" => $this->currentTransition];
		}

		return $this;
	}

	public function integer(string $string): StateMachine
	{
		$this->integers[$string] = 0;

		return $this;
	}

	public function set(string $string, int $i): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setOperation(
				operation: Operation::SET,
				variable: $string,
				value: $i
			);
			$this->currentTransition->setOperationVariable($string);
			$this->currentTransition->setOperationValue($i);
		}

		return $this;
	}

	public function increment(string $string): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setOperation(
				operation: Operation::INCREMENT,
				variable: $string
			);
		}
		return $this;
	}

	public function decrement(string $string): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setOperation(
				operation: Operation::DECREMENT,
				variable: $string
			);
		}
		return $this;
	}

	public function ifEquals(string $string, int $i): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setConditional(
				condition: Condition::EQUALS,
				variable: $string,
				value: $i,
			);
		}
		return $this;
	}

	public function ifGreaterThan(string $string, int $i): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setConditional(
				condition: Condition::GREATER_THAN,
				variable: $string,
				value: $i
			);
		}
		return $this;
	}

	public function ifLessThan(string $string, int $i): StateMachine
	{
		if ($this->currentTransition != null) {
			$this->currentTransition->setConditional(
				condition: Condition::LESS_THAN,
				variable: $string,
				value: $i
			);
		}
		return $this;
	}

}

