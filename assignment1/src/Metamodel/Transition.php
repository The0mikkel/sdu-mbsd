<?php

namespace TheMikkel\Assignment1\Metamodel;

use TheMikkel\Assignment1\Types\Condition;
use TheMikkel\Assignment1\Types\TransitionOperation;

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
	 * @var TransitionOperation|null $operation
	 */
	private TransitionOperation|null $operation = null;
	/**
	 * Operation variable name.
	 * 
	 * @var string|null
	 */
	private string|null $operationVariableName = null;

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
	private Condition|null $condition = null;
	/**
	 * Condition variable name.
	 * 
	 * @var string|null
	 */
	private string|null $conditionVariableName = null;
	/**
	 * Condition compared value.
	 * 
	 * @var int
	 */
	private int $conditionComparedValue = 0;


	public function __construct(
		string $event,
		State $target = null,
		TransitionOperation|string|null $operation = null,
		string|null $operationVariable = null,
		int $operationValue = 0,
		Condition|string|null $condition = null,
		string|null $conditionVariable = null,
		int $conditionComparedValue = 0
	) {
		$this->event = $event;
		$this->target = $target;

		if (is_string($operation)) { # Convert string to TransitionOperation
			$operation = TransitionOperation::from($operation) ?? null;
		}
		$this->operation = $operation;
		$this->operationVariableName = $operationVariable;
		$this->operationVariableValue = $operationValue;

		if (is_string($condition)) {
			$condition = Condition::from($condition) ?? null;
		}
		$this->condition = $condition;
		$this->conditionVariableName = $conditionVariable;
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

	public function setOperation(?TransitionOperation $operation, ?string $variable = null, ?int $value = null): void
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
		$this->operationVariableName = $variable;
	}

	public function setOperationValue(int $value): void
	{
		$this->operationVariableValue = $value;
	}

	public function getOperation(): TransitionOperation
	{
		return $this->operation ?? TransitionOperation::NONE;
	}

	public function hasSetOperation(): bool
	{
		return $this->operation == TransitionOperation::SET;
	}

	public function hasIncrementOperation(): bool
	{
		return $this->operation == TransitionOperation::INCREMENT;
	}

	public function hasDecrementOperation(): bool
	{
		return $this->operation == TransitionOperation::DECREMENT;
	}

	public function getOperationVariableName(): string|null
	{
		return $this->operationVariableName;
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
		$this->conditionVariableName = $variable;
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
		return $this->conditionVariableName;
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
