<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Poll_user
 *
 * @property int $poll_id
 * @property int $user_id
 * @property int $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Poll_user wherePollId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Poll_user whereUserId($value)
 * @mixin \Eloquent
 */
class Poll_user extends Model {

	protected $table = 'poll_user';
	public $timestamps = false;
	protected $fillable = ['poll_id', 'user_id'];

}