<?php

namespace TheMikkel\Assignment1\Types;

enum TransitionOperation: string {
	case NONE = "None";
	case SET = "Set";
	case INCREMENT = "Increment";
	case DECREMENT = "Decrement";
}