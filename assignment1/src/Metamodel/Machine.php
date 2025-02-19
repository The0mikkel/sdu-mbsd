<?php

namespace TheMikkel\Assignment1\Metamodel;

class Machine
{
	/**
	 * States, stored as a map
	 * 
	 * @var array $states
	 * 
	 * @example
	 * [
	 *   "name" => new State(),
	 *   "name2" => new State()
	 * ]
	 */
	private array $states = [];

	/**
	 * Initial state.
	 * 
	 * @var string $initialState
	 * 
	 * @example
	 * 'state1'
	 */
	private ?State $initialState = null;

	/**
	 * Integers, stored as a map of variable name to value.
	 * 
	 * @var array $integers
	 * 
	 * @example
	 * [
	 *    'x' => 5,
	 *    'y' => 10
	 * ]
	 */
	private array $integers = [];

	public function __construct(
		array $states = [],
		State $initialState = null,
		array $integers = []
	) {
		$this->states = $states;
		$this->initialState = $initialState ?? null;
		$this->integers = $integers;
	}

	public function getStates(): array
	{
		return $this->states;
	}

	public function getInitialState(): State
	{
		return $this->initialState;
	}

	public function getState(string $name): State|null
	{
		if (array_key_exists($name, $this->states)) {
			return $this->states[$name];
		}

		return null;
	}

	public function numberOfIntegers(): int
	{
		return count($this->integers);
	}

	public function hasInteger(string $string): bool
	{
		return array_key_exists($string, $this->integers);
	}

	public function getInteger(string $string): int
	{
		if (!$this->hasInteger($string)) {
			throw new \Exception("No integer found for variable: " . $string);
		}
		return $this->integers[$string];
	}

	public function setInteger(string $string, int $value): void
	{
		if (!$this->hasInteger($string)) {
			throw new \Exception("No integer found for variable: " . $string);
		}

		$this->integers[$string] = $value;
	}
}
