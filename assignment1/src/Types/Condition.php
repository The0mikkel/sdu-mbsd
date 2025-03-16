<?php

namespace TheMikkel\Assignment1\Types;

enum Condition: string {
	case NONE = "None";
	case EQUALS = "Equals";
	case GREATER_THAN = "Greater than";
	case LESS_THAN = "Less than";
}