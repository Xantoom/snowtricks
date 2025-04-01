<?php

namespace App\Enum;

enum SnowtrickCategories: string
{
	case AERIAL_MASTERY = 'Aerial Mastery';
	case GROUND_GAME = 'Ground Game';
	case RAIL_AND_BOX_WIZARDRY = 'Rail and Box Wizardry';
	case TRANSITION_TECHNIQUES = 'Transition Techniques';
	case PROGRESSION_PATHWAYS = 'Progression Pathways';

	public function getBootstrapColor(): string
	{
		return match ($this) {
			self::AERIAL_MASTERY => 'primary',
			self::GROUND_GAME => 'success',
			self::RAIL_AND_BOX_WIZARDRY => 'warning',
			self::TRANSITION_TECHNIQUES => 'info',
			self::PROGRESSION_PATHWAYS => 'danger',
		};
	}
}
