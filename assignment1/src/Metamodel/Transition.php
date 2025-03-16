<?php

namespace TheMikkel\Assignment1\Metamodel;

use TheMikkel\Assignment1\Types\Condition;
use TheMikkel\Assignment1\Types\Operation;

class Transition
{
	/**
	 * Event, stored as a string.
	 * 
	 * @var string $event
	 * 
	 * @example
	 * 'event1'
	 */
	private string $event;

	/**
	 * Target state.
	 * 
	 * @var State $target
	 */
	private ?State $target;

	/**
	 * Operation, stored as a TransitionOperation.
	 * 
	 * @var Operation|null $operation
	 */
	private ?Operation $operation = null;
	/**
	 * Operation variable name.
	 * 
	 * @var string|null
	 */
	private ?string $operationVariable = null;

	/**
	 * Operation set value
	 * 
	 * @var int
	 */
	private int $operationVariableValue = 0;

	/**
	 * Condition, stored as a Condition.
	 * 
	 * @var Condition|null $condition
	 */
	private ?Condition $condition = null;
	/**
	 * Condition variable name.
	 * 
	 * @var string|null
	 */
	private ?string $conditionVariable = null;
	/**
	 * Condition compared value.
	 * 
	 * @var int
	 */
	private int $conditionComparedValue = 0;


	public function __construct(
		string $event,
		State $target = null,
		Operation|string|null $operation = null,
		string|null $operationVariable = null,
		int $operationValue = 0,
		Condition|string|null $condition = null,
		string|null $conditionVariable = null,
		int $conditionComparedValue = 0
	) {
		$this->event = $event;
		$this->target = $target;

		if (is_string($operation)) { # Convert string to TransitionOperation
			$operation = Operation::from($operation) ?? null;
		}
		$this->operation = $operation;
		$this->operationVariable = $operationVariable;
		$this->operationVariableValue = $operationValue;

		if (is_string($condition)) {
			$condition = Condition::from($condition) ?? null;
		}
		$this->condition = $condition;
		$this->conditionVariable = $conditionVariable;
		$this->conditionComparedValue = $conditionComparedValue;
	}

	public function getEvent(): string
	{
		return $this->event;
	}

	public function getTarget(): State
	{
		return $this->target;
	}

	public function setTarget(State $target): void
	{
		$this->target = $target;
	}

	public function setOperation(?Operation $operation, ?string $variable = null, ?int $value = null): void
	{
		$this->operation = $operation;

		if ($variable != null) {
			$this->setOperationVariable($variable);
		}
		if ($value != null) {
			$this->setOperationValue($value);
		}
	}

	public function setOperationVariable(string $variable): void
	{
		$this->operationVariable = $variable;
	}

	public function setOperationValue(int $value): void
	{
		$this->operationVariableValue = $value;
	}

	public function getOperation(): Operation
	{
		return $this->operation ?? Operation::NONE;
	}

	public function hasSetOperation(): bool
	{
		return $this->operation == Operation::SET;
	}

	public function hasIncrementOperation(): bool
	{
		return $this->operation == Operation::INCREMENT;
	}

	public function hasDecrementOperation(): bool
	{
		return $this->operation == Operation::DECREMENT;
	}

	public function getOperationVariableName(): string|null
	{
		return $this->operationVariable;
	}

	public function getOperationValue(): int
	{
		return $this->operationVariableValue;
	}

	public function setConditional(
		?Condition $condition,
		?string $variable = null,
		?int $value = null
	): void {
		$this->condition = $condition;

		if ($variable != null) {
			$this->setConditionalVariable($variable);
		}
		if ($value != null) {
			$this->setConditionalComparedValue($value);
		}
	}

	public function setConditionalVariable(string $variable): void
	{
		$this->conditionVariable = $variable;
	}

	public function setConditionalComparedValue(string $value): void
	{
		$this->conditionComparedValue = $value;
	}

	public function isConditional(): bool
	{
		if (
			!$this->condition
			|| $this->condition == Condition::NONE
		) {
			return false;
		}

		return true;
	}

	public function getConditionVariableName(): string|null
	{
		return $this->conditionVariable;
	}

	public function getConditionComparedValue(): int
	{
		return $this->conditionComparedValue;
	}

	public function getCondition(): ?Condition
	{
		return $this->condition;
	}

	public function isConditionEqual(): bool
	{
		return $this->condition == Condition::EQUALS;
	}

	public function isConditionGreaterThan(): bool
	{
		return $this->condition == Condition::GREATER_THAN;
	}

	public function isConditionLessThan(): bool
	{
		return $this->condition == Condition::LESS_THAN;
	}

	public function hasOperation(): bool
	{
		return $this->operation != null;
	}

}
