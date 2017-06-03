<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property int $list_log_id
 * @property string $message
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\List_log $list_log
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Log whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Log whereListLogId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Log whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Log whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Log extends Model {

	protected $table = 'logs';
	public $timestamps = true;
	protected $fillable = array('list_log_id', 'message');

	public function list_log()
	{
		return $this->belongsTo('App\Models\List_log');
	}

}