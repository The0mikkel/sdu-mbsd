<?php

namespace TheMikkel\Assignment1\Types;

enum Operation: string {
	case NONE = "None";
	case SET = "Set";
	case INCREMENT = "Increment";
	case DECREMENT = "Decrement";
}