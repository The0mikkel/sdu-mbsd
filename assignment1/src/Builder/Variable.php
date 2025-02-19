<?php

namespace TheMikkel\Assignment1\Builder;

abstract class Variable
{
	private string $variable;

	public function __construct(
		string $variable
	) {
		$this->variable = $variable;
	}

	public function getVariable(): string
	{
		return $this->variable;
	}
}