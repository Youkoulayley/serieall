<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\Episode
 *
 * @property int $id
 * @property int $thetvdb_id
 * @property int $numero
 * @property string $name
 * @property string $name_fr
 * @property string $resume
 * @property string $resume_fr
 * @property string $particularite
 * @property string $diffusion_us
 * @property string $diffusion_fr
 * @property string $ba
 * @property float $moyenne
 * @property int $nbnotes
 * @property int $season_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Season $season
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Artist[] $artists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $articles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Artist[] $directors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Artist[] $writers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Artist[] $guests
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereThetvdbId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereNumero($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereNameFr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereResume($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereResumeFr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereParticularite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereDiffusionUs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereDiffusionFr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereBa($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereMoyenne($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereNbnotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereSeasonId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Episode whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $picture
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Episode wherePicture($value)
 */
class Episode extends Model {
    use BelongsToThrough;

	protected $table = 'episodes';
	public $timestamps = true;
	protected $fillable = ['thetvdb_id', 'numero', 'name', 'name_fr', 'resume', 'resume_fr', 'diffusion_us', 'diffusion_fr', 'ba', 'moyenne', 'nbnotes'];

    /**
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     * @throws \Exception
     * @throws \Exception
     */
    public function show()
    {
        return $this->belongsToThrough(Show::class, Season::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function season()
    {
        return $this->belongsTo('App\Models\Season');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function artists()
	{
		return $this->morphToMany('App\Models\Artist', 'artistable');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
	{
		return $this->morphMany('App\Models\Comment', 'commentable');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
	{
		return $this->belongsToMany('App\Models\User')->withPivot('rate', 'created_at', 'updated_at');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function articles()
	{
		return $this->morphToMany('App\Models\Article', 'articlable');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function directors()
	{
		return $this->morphToMany('App\Models\Artist', 'artistable')->wherePivot('profession', 'director');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function writers()
	{
		return $this->morphToMany('App\Models\Artist', 'artistable')->wherePivot('profession','writer');
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function guests()
	{
		return $this->morphToMany('App\Models\Artist', 'artistable')->wherePivot('profession', 'guest');
	}

}