<?php


namespace App\Repositories;

use App\Models\Episode_user;
use App\Models\Season;

/**
 * Class SeasonRepository
 * @package App\Repositories\Admin
 */
class SeasonRepository
{
    /**
    * @var Season
    */
    protected $season;

    /**
     * SeasonRepository constructor.
     *
     * @param Season $season
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    /**
     * Récupère les saisons d'une série grâce à son ID.
     * On ajoute également le nombre d'épisodes.
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]|Season
     */
    public function getSeasonsCountEpisodesForShowByID($id){
        return $this->season->where('show_id', '=', $id)
            ->withCount('episodes')
            ->with(['comments' => function($q){
                $q->select('id', 'thumb', 'commentable_id', 'commentable_type', \DB::Raw("count(*) as count_thumb"));
                $q->groupBy('thumb');
            }])
            ->orderBy('seasons.name', 'asc')
            ->get();
    }

    /**
     * Récupère une saison par son ID
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|Season
     */
    public function getSeasonByID($id)
    {
        return $this->season->findOrFail($id);
    }

    /**
     * Récupère une saison, la série associée et les épisodes associés via l'ID de la saison
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|Season
     */
    public function getSeasonShowEpisodesBySeasonID($id)
    {
        return $this->season
            ->with('show', 'episodes')
            ->findOrFail($id);
    }

    /**
     * Récupère une saison via son ID et récuèper également la série associée
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|Season
     */
    public function getSeasonWithShowByID($id)
    {
        return $this->season
            ->with('show')
            ->findOrFail($id);
    }

    /**
     * @param $showID
     * @param $seasonName
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSeasonEpisodesBySeasonNameAndShowID($showID, $seasonName)
    {
        return $this->season
            ->with(['episodes' => function($q){
                $q->with(['comments' => function($q){
                    $q->select('id', 'thumb', 'commentable_id', 'commentable_type', \DB::Raw("count(*) as count_thumb"));
                    $q->groupBy('thumb');
                }]);
            }])
            ->withCount('episodes')
            ->where('seasons.name', '=', $seasonName)
            ->where('seasons.show_id', '=', $showID)
            ->first();
    }

    /**
     * Récupère la note de la saison en cours
     *
     * @param $id
     * @return array
     */
    public function getRateBySeasonID($id)
    {
        return $this->season->with(['users' => function($q){
                $q->orderBy('updated_at', 'desc');
                $q->limit(20);
            }, 'users.episode' => function($q){
                $q->select('id', 'numero');
            }, 'users.user' => function($q){
                $q->select('id', 'username', 'email');
            }])
            ->where('id', '=', $id)
            ->first()
            ->toArray();
    }
}
