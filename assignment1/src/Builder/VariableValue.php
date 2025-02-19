<?php

namespace TheMikkel\Assignment1\Builder;

abstract class VariableValue
{
	private string $variable;
	private int $value;

	public function __construct(
		string $variable,
		int $value
	) {
		$this->variable = $variable;
		$this->value = $value;
	}

	public function getVariable(): string
	{
		return $this->variable;
	}

	public function getValue(): int
	{
		return $this->value;
	}
}