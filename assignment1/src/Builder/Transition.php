<?php

namespace TheMikkel\Assignment1\Builder;

use TheMikkel\Assignment1\Types\Condition;
use TheMikkel\Assignment1\Types\Operation;

class Transition
{
	private string $name = "";
	private string $target = "";
	private ?Operation $operation = null;
	private ?string $operationVariable = null;
	private int $operationValue = 0;
	private ?Condition $condition = null;
	private ?string $conditionVariable = null;
	private ?int $conditionValue = 0;

	/**
	 * Create transition, with dynamicly interpreted arguments
	 * 
	 * @param string $when When this transition is done. Also seen as the event name that triggers the transition.
	 * @param string $to What state it transitions to. Must be the name of the state.
	 * @param Set|Increment|Decrement|IfEquals|IfGreaterThan|IfLessThan $args List of arguments for the operation and condition for the transition.
	 */
	public function __construct(
		string $when,
		string $to,
		Set|Increment|Decrement|IfEquals|IfGreaterThan|IfLessThan ...$args
	) {
		// Set main attributes
		$this->name = $when;
		$this->target = $to;

		// Append any nested list, to the end of args
		$listArgs = array_filter($args, function ($arg) {
			return is_array($arg);
		});
		$args = array_merge($args, $listArgs);

		// Go through each additional argument, and add data to corresponding attributes
		foreach ($args as $value) {
			// Ensure value is an object, as args should be objects of specific classes
			if (!is_object($value)) {
				continue;
			}

			// Get type (class) of the value under review
			$class = get_class($value);

			// Handle variable only transition types
			if ($value instanceof Variable) {
				$this->operationVariable = $value->getVariable();
				$this->operation = match ($class) {
					Increment::class => Operation::INCREMENT,
					Decrement::class => Operation::DECREMENT,
					default => Operation::NONE,
				};
			} else {
				// Handle variable and value transition types
				if ($value instanceof VariableValue) {
					switch ($class) {
						case Set::class: # Transition set operation
							$this->operation = Operation::SET;
							$this->operationVariable = $value->getVariable();
							$this->operationValue = $value->getValue();
							break;

						default: # Transition conditionals
							$this->condition = match ($class) {
								IfEquals::class => Condition::EQUALS,
								IfGreaterThan::class => Condition::GREATER_THAN,
								IfLessThan::class => Condition::LESS_THAN,
								default => Condition::NONE,
							};
							$this->conditionVariable = $value->getVariable();
							$this->conditionValue = $value->getValue();
							break;
					}
				}
			}
		}
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getTarget(): ?string
	{
		return $this->target;
	}

	public function getOperation(): ?Operation
	{
		return $this->operation;
	}

	public function getOperationVariable(): ?string
	{
		return $this->operationVariable;
	}

	public function getOperationValue(): int
	{
		return $this->operationValue;
	}

	public function getCondition(): ?Condition
	{
		return $this->condition;
	}

	public function getConditionVariable(): ?string
	{
		return $this->conditionVariable;
	}

	public function getConditionValue(): ?int
	{
		return $this->conditionValue;
	}
}