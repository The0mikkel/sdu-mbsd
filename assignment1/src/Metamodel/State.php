<?php

namespace TheMikkel\Assignment1\Metamodel;

class State
{
	/**
	 * Name of the state.
	 * 
	 * @var string $name
	 * 
	 * @example
	 * 'state1'
	 */
	private ?string $name = null;

	/**
	 * Transitions, stored as a list.
	 * 
	 * @var array
	 */
	private array $transitions = [];

	public function __construct(
		string $name = null,
		array $transitions = []
	) {
		$this->name = $name;
		$this->transitions = $transitions;
	}

	public function addTransition(Transition $transition)
	{
		$this->transitions[] = $transition;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getTransitions(): array
	{ # List<Transition>
		return $this->transitions;
	}

	public function getTransitionByEvent(string $string): array
	{
		$transitions = [];
		foreach ($this->transitions as $transition) {
			if ($transition->getEvent() == $string) {
				$transitions[] = $transition;
			}
		}
		return $transitions;
	}

}