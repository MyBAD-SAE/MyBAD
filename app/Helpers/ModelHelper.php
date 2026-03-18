<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassParticipant> $classParticipants
 * @property-read int|null $class_participants_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdminUser whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAdminUser {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $min_diff
 * @property int $max_diff
 * @property float $winner_points
 * @property int $school_class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolClass $schoolClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereMaxDiff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereMinDiff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereSchoolClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlgorithmParameter whereWinnerPoints($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlgorithmParameter {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $participantable_type
 * @property string $participantable_id
 * @property numeric $elo_rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $school_class_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $participantable
 * @property-read \App\Models\SchoolClass $schoolClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereEloRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereParticipantableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereParticipantableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereSchoolClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassParticipant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClassParticipant {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $school_class_id
 * @property \Illuminate\Support\Carbon $date
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameMatch> $gameMatches
 * @property-read int|null $game_matches_count
 * @property-read \App\Models\SchoolClass $schoolClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereSchoolClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClassSession whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperClassSession {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $player_id
 * @property numeric $elo_before
 * @property numeric $elo_after
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory whereEloAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory whereEloBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EloHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEloHistory {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $class_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSession $classSession
 * @property-read \App\Models\MatchPlayer|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Player> $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch whereClassSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameMatch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGameMatch {}
}

namespace App\Models{
/**
 * @property int $game_match_id
 * @property string $player_id
 * @property int $score
 * @property bool $validated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GameMatch $gameMatch
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer whereGameMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchPlayer whereValidated($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMatchPlayer {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $pin
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassParticipant> $classParticipants
 * @property-read int|null $class_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EloHistory> $eloHistories
 * @property-read int|null $elo_histories_count
 * @property-read \App\Models\MatchPlayer|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameMatch> $gameMatches
 * @property-read int|null $game_matches_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPlayer {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $access_token
 * @property int $school_class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolClass $schoolClass
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView whereSchoolClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicView whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPublicView {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $school_year
 * @property string $name
 * @property string|null $address
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AlgorithmParameter> $algorithmParameters
 * @property-read int|null $algorithm_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassParticipant> $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\PublicView|null $publicView
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSession> $sessions
 * @property-read int|null $sessions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereSchoolYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SchoolClass whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSchoolClass {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $profile_picture
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdminUser|null $adminUser
 * @property-read string|null $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Player|null $player
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

