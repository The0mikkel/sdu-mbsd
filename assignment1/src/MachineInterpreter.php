<?php

namespace TheMikkel\Assignment1;

use TheMikkel\Assignment1\Metamodel\Machine;
use TheMikkel\Assignment1\Metamodel\State;
use TheMikkel\Assignment1\Metamodel\Transition;
use TheMikkel\Assignment1\Types\Condition;
use TheMikkel\Assignment1\Types\TransitionOperation;


class MachineInterpreter
{
	private Machine $machine;
	private State $currentState;

	public function run(Machine $m)
	{
		$this->machine = $m;
		$this->currentState = $m->getInitialState();
	}

	public function getCurrentState(): State
	{
		return $this->currentState;
	}

	public function processEvent(string $string)
	{
		$transitions = $this->currentState->getTransitionByEvent($string);

		foreach ($transitions as $transition) {
			if ($transition == null) {
				return;
			}

			if (
				!$transition->isConditional()
				|| (
					$transition->isConditional()
					&& $this->processConditional($transition)
				)
			) {
				$this->currentState = $transition->getTarget();
				$this->processOperation($transition);
				return;
			}
		}
	}

	private function processConditional(Transition $transition): bool
	{
		$condition = $transition->getCondition();
		$variable = $transition->getConditionVariableName();

		if (!$variable) {
			throw new \Exception("Variable empty. Cannot process conditional for " . $transition->getEvent() . " with " . ($transition->getCondition()?->name ?? "Unknown"));
		}

		$value = $this->machine->getInteger($variable);
		$comparedValue = $transition->getConditionComparedValue();

		switch ($condition) {
			case Condition::NONE:
			case null:
			default:
				break;

			case Condition::EQUALS:
				if ($value == $comparedValue) {
					return true;
				}
				break;
			case Condition::GREATER_THAN:
				if ($value > $comparedValue) {
					return true;
				}
				break;
			case Condition::LESS_THAN:
				if ($value < $comparedValue) {
					return true;
				}
				break;
		}

		return false;
	}

	private function processOperation(Transition $transition): void
	{
		$operation = $transition->getOperation();
		if ($operation == null || $operation == TransitionOperation::NONE) {
			return;
		}

		$variable = $transition->getOperationVariableName();
		if (!$variable) {
			throw new \Exception("Variable empty. Cannot process operation " . $transition->getEvent() . " with " . ($transition->getCondition()?->name ?? "Unknown"));
		}

		$value = $this->machine->getInteger($variable);
		$operationValue = $transition->getOperationValue();

		switch ($operation) {
			case TransitionOperation::NONE:
			case null:
			default:
				break;

			case TransitionOperation::SET:
				$this->machine->setInteger($variable, $operationValue);
				break;

			case TransitionOperation::DECREMENT:
				$this->machine->setInteger($variable, $value - 1);
				break;

			case TransitionOperation::INCREMENT:
				$this->machine->setInteger($variable, $value + 1);
				break;
		}
	}

	public function getInteger(string $string): int
	{
		return $this->machine->getInteger($string);
	}

}