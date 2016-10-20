<?php

namespace App\Jobs;


use App\Models\Channel;
use App\Models\Nationality;
use App\Models\Show;
use App\Models\Genre;
use App\Models\Artist;
use App\Models\Temp;
use App\Models\Season;
use App\Models\Episode;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use GuzzleHttp\Client;
use \Illuminate\Support\Str;

class UpdateShowFromTVDB extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    private function UpdateEpisodeOneByOne($client, $getEpisodes, $api_version, $token, $serieInBDD)
    {
        foreach ($getEpisodes as $episode) {
            # On vérifie d'abord que la saison n'est pas à 0
            $seasonNumber = $episode->airedSeason;

            if ($seasonNumber != 0) {
                # On récupère l'ID de l'épisode
                $episodeID = $episode->id;

                /*
                |--------------------------------------------------------------------------
                | Récupération des informations de l'épisode en question
                |--------------------------------------------------------------------------
                | Dans un premier temps, en français.
                | Puis en anglais et on vérifie que le français est bien rempli, sinon on
                | choisit la version anglaise.
                */
                $getEpisode_fr = $client->request('GET', '/episodes/' . $episodeID, [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                        'Accept-Language' => 'fr',
                    ]
                ])->getBody();

                $getEpisode_en = $client->request('GET', '/episodes/' . $episodeID, [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                        'Accept-Language' => 'en',
                    ]
                ])->getBody();

                # On décode le JSON
                $getEpisode_fr = json_decode($getEpisode_fr);
                $getEpisode_en = json_decode($getEpisode_en);

                $getEpisode_en = $getEpisode_en->data;
                $getEpisode_fr = $getEpisode_fr->data;

                # Si l'épisode a été mis à jour depuis la dernière fois
                $lastUpdate = Temp::where('key', 'last_update')->first();
                $lastUpdate = $lastUpdate->value;

                $episode_ref = Episode::where('thetvdb_id', $episodeID)->first();

                if ($lastUpdate <= $getEpisode_en->lastUpdated || is_null($episode_ref)) {
                    $episodeNumero = $getEpisode_en->airedEpisodeNumber;
                    Log::info('** Modification de l\'épisode n°' . $seasonNumber . 'x' . $episodeNumero . ' **');

                    /*
                    |--------------------------------------------------------------------------
                    | Récupération des informations de la saison
                    |--------------------------------------------------------------------------
                    | On crée la saison si elle n'existe pas
                    */
                    # Variables de la saison
                    $seasonID = $getEpisode_en->airedSeasonID;
                    $seasonName = $getEpisode_en->airedSeason;

                    # Vérification de la présence de la saison dans la BDD
                    $season_ref = Season::where('thetvdb_id', $seasonID)->first();

                    # Si elle n'existe pas
                    if (is_null($season_ref)) {
                        Log::info('Création de la saison');

                        # On prépare la nouvelle saison
                        $season_ref = new Season([
                            'name' => $seasonName,
                            'thetvdb_id' => $seasonID
                        ]);

                        # Et on la sauvegarde en passant par l'objet Show pour créer le lien entre les deux
                        $season_ref->show()->associate($serieInBDD);
                        $season_ref->save();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Récupération des informations de l'épisode
                    |--------------------------------------------------------------------------
                    | On crée l'épisode si elle n'existe pas
                    */

                    # Vérification de la présence de l'épisode dans la BDD
                    $episode_ref = Episode::where('thetvdb_id', $episodeID)->first();


                    # Si il n'existe pas
                    if (is_null($episode_ref)) {
                        # Variables de l'épisode
                        # Nom de l'épisode (s'il n'existe pas on met le nom par défaut
                        $episodeName = $getEpisode_en->episodeName;
                        if (is_null($episodeName)) {
                            $episodeName = 'TBA';
                        }

                        # Date de diffusion US. Si elle n'existe pas, on met la date par défaut
                        $episodeDiffusionUS = $getEpisode_en->firstAired;
                        if (is_null($episodeDiffusionUS)) {
                            $episodeDiffusionUS = '1800-01-01';
                        }

                        # Nom FR, sil n'existe pas, on en met pas
                        $episodeNameFR = $getEpisode_fr->episodeName;
                        if (is_null($episodeNameFR)) {
                            $episodeNameFR = 'TBA';
                        }

                        # Résumé, si pas de version française, on met la version anglaise, et sinon on met le résumé par défaut
                        $episodeResume = $getEpisode_fr->overview;
                        if (is_null($episodeResume)) {
                            $episodeResume = $getEpisode_en->overview;
                            if (is_null($episodeResume)) {
                                $episodeResume = 'TBA';
                            }
                        }

                        Log::info('Création de l\'épisode n°' . $episodeNumero);

                        # On prépare le nouvel épisode
                        $episode_ref = new Episode([
                            'numero' => $episodeNumero,
                            'name' => $episodeName,
                            'name_fr' => $episodeNameFR,
                            'thetvdb_id' => $episodeID,
                            'resume' => $episodeResume,
                            'diffusion_us' => $episodeDiffusionUS,
                        ]);
                        # Et on le sauvegarde en passant par l'objet Season pour créer le lien entre les deux
                        $episode_ref->season()->associate($season_ref);
                        $episode_ref->save();
                    } else {
                        /*
                        |--------------------------------------------------------------------------
                        | On va chercher les modifications qui pourraient avoir eu lieu
                        | et qui nous intéresse.
                        |--------------------------------------------------------------------------
                        */

                        $nomENEpisode = $episode_ref->name;
                        # Si le nom FR est à TBA dans notre base
                        if ($nomENEpisode == 'TBA') {
                            # On vérifie si le nom est rempli en FR
                            if (!is_null($getEpisode_en->episodeName)) {
                                # On sauvegarde le nom en français
                                Log::info('Mise à jour du nom en version originale.');
                                $episode_ref->name = $getEpisode_en->episodeName;
                            }
                        }

                        $nomFREpisode = $episode_ref->name_fr;
                        # Si le nom FR est à TBA dans notre base
                        if ($nomFREpisode == 'TBA') {
                            # On vérifie si le nom est rempli en FR
                            if (!is_null($getEpisode_fr->episodeName)) {
                                # On sauvegarde le nom en français
                                Log::info('Mise à jour du nom en français.');
                                $episode_ref->name_fr = $getEpisode_fr->episodeName;
                            }
                        }

                        $diffusionEpisode = $episode_ref->diffusion_us;
                        # Si la diffusion est renseignée sur theTVDB
                        if (!empty($getEpisode_en->firstAired)) {
                            # Si la diffusion dans notre BDD est égale à celle dans TheTVDB
                            if ($diffusionEpisode != $getEpisode_en->firstAired) {
                                # On enregistre la nouvelle diffusion
                                Log::info('Mise à jour de la diffusion US.');
                                $episode_ref->diffusion_us = $getEpisode_en->firstAired;
                            }
                        }

                        $resumeEpisode = $episode_ref->resume;
                        # Si le résumé est à TBA dans notre base
                        if ($resumeEpisode == 'TBA') {
                            # On vérifie si le résumé est rempli en FR
                            if (!is_null($getEpisode_fr->overview)) {
                                # On sauvegarde le résumé en français
                                Log::info('Mise à jour du résumé en français.');
                                $episode_ref->resume = $getEpisode_fr->overview;
                            } else {
                                # On vérifie que le résumé est rempli en EN
                                if (!is_null($getEpisode_en->overview)) {
                                    # On sauvegarde le résumé en EN
                                    Log::info('Mise à jour du résumé en anglais.');
                                    $episode_ref->resume = $getEpisode_en->overview;
                                }
                            }
                        }

                        # On sauvegarde les modifs
                        $episode_ref->save();

                        /*
                        |--------------------------------------------------------------------------
                        | Récupération des informations sur les scénaristes de l'épisode
                        |--------------------------------------------------------------------------
                        | On crée les scénaristes s'ils n'existent pas et on les lie à l'épisode
                        */
                        $writers = $getEpisode_en->writers;
                        if (!empty($writers)) {
                            # Pour chaque scénariste
                            foreach ($writers as $writer) {
                                # On supprime les espaces
                                $writer = trim($writer);
                                # On met en forme l'URL
                                $writer_url = Str::slug($writer);
                                # On vérifie si le scénariste existe déjà en base
                                $writer_ref = Artist::where('artist_url', $writer_url)->first();

                                # Si il n'existe pas
                                if (is_null($writer_ref)) {
                                    Log::info('Création du scénariste ' . $writer);
                                    # On prépare le nouveau scénariste
                                    $writer_ref = new Artist([
                                        'name' => $writer,
                                        'artist_url' => $writer_url
                                    ]);

                                    # Et on le sauvegarde ne passant par l'objet Episode pour créer le lien entre les deux
                                    $episode_ref->artists()->save($writer_ref, ['profession' => 'writer']);

                                } else {
                                    # On vérifie que le scénariste n'est pas déjà lié à la série
                                    $writer_liaison = $writer_ref->episodes()
                                        ->where('episodes.thetvdb_id', $episodeID)
                                        ->where('artistables.profession', 'writer')
                                        ->get();

                                    if (empty($writer_liaison)) {
                                        # On lie l'acteur à la série
                                        Log::info('Le scénariste ' . $writer . ' existe déjà mais n\'est pas lié à la série. On le lie.');
                                        $episode_ref->artists()->attach($writer_ref->id, ['profession' => 'writer']);
                                    }
                                }
                            }
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Récupération des informations sur les réalisateurs de l'épisode
                        |--------------------------------------------------------------------------
                        | On crée les réals s'ils n'existent pas et on les lie à l'épisode
                        */
                        $directors = $getEpisode_en->directors;
                        if (!empty($directors)) {
                            # Pour chaque réal
                            foreach ($directors as $director) {
                                # On supprime les espaces
                                $director = trim($director);
                                # On met en forme l'URL
                                $director_url = Str::slug($director);
                                # On vérifie si le réal existe déjà en base
                                $director_ref = Artist::where('artist_url', $director_url)->first();

                                # Si il n'existe pas
                                if (is_null($director_ref)) {
                                    Log::info('Création du réalisateur ' . $director);
                                    # On prépare le nouveau réal
                                    $director_ref = new Artist([
                                        'name' => $director,
                                        'artist_url' => $director_url
                                    ]);

                                    # Et on le sauvegarde ne passant par l'objet Episode pour créer le lien entre les deux
                                    $episode_ref->artists()->save($director_ref, ['profession' => 'director']);

                                } else {
                                    # On vérifie que le scénariste n'est pas déjà lié à la série
                                    $writer_liaison = $director_ref->episodes()
                                        ->where('episodes.thetvdb_id', $episodeID)
                                        ->where('artistables.profession', 'director')
                                        ->get();

                                    if (empty($writer_liaison)) {
                                        # On lie l'acteur à la série
                                        Log::info('Le scénariste ' . $director . ' existe déjà mais n\'est pas lié à la série. On le lie.');
                                        $episode_ref->artists()->attach($director_ref->id, ['profession' => 'director']);
                                    }
                                }
                            }

                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Récupération des informations sur les guests de l'épisode
                        |--------------------------------------------------------------------------
                        | On crée les guests s'ils n'existent pas et on les lie à l'épisode
                        */
                        $guestStars = $getEpisode_en->guestStars;
                        if (!empty($guestStars)) {
                            # Pour chaque guest
                            foreach ($guestStars as $guestStar) {
                                # On supprime les espaces
                                $guestStar = trim($guestStar);
                                # On met en forme l'URL
                                $guestStar_url = Str::slug($guestStar);
                                # On vérifie si le guest existe déjà en base
                                $guestStar_ref = Artist::where('artist_url', $guestStar_url)->first();

                                # Si il n'existe pas
                                if (is_null($guestStar_ref)) {
                                    Log::info('Création du guest ' . $guestStar);
                                    # On prépare le nouveau guest
                                    $guestStar_ref = new Artist([
                                        'name' => $guestStar,
                                        'artist_url' => $guestStar_url
                                    ]);

                                    # Et on le sauvegarde ne passant par l'objet Episode pour créer le lien entre les deux
                                    $episode_ref->artists()->save($guestStar_ref, ['profession' => 'guest']);

                                } else {
                                    # On vérifie que le scénariste n'est pas déjà lié à la série
                                    $guest_liaison = $guestStar_ref->episodes()
                                        ->where('episodes.thetvdb_id', $episodeID)
                                        ->where('artistables.profession', 'guest')
                                        ->get();

                                    if (empty($guest_liaison)) {
                                        # On lie l'acteur à la série
                                        Log::info('Le scénariste ' . $director . ' existe déjà mais n\'est pas lié à la série. On le lie.');
                                        $episode_ref->artists()->attach($guestStar_ref->id, ['profession' => 'guest']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }



    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('>>>>>>>>>> Lancement du job d\'update <<<<<<<<<<');
        /*
        |--------------------------------------------------------------------------
        | Définition des variables
        |--------------------------------------------------------------------------
        */
        $secondsWeek = 604800;
        $key_token = "token";
        $api_key = config('thetvdb.apikey');
        $api_username = config('thetvdb.username');
        $api_userkey = config('thetvdb.userkey');
        $api_url = config('thetvdb.url');
        $api_version = config('thetvdb.version');
        $hours_duration_token = config('thetvdb.hoursduration');

        /*
        |--------------------------------------------------------------------------
        | Création du client
        |--------------------------------------------------------------------------
        */
        $client = new Client(['base_uri' => $api_url]);

        /*
        |--------------------------------------------------------------------------
        | Requête d'authentification
        |--------------------------------------------------------------------------
        | L'objectif est de récupérer un token d'identification si le dernier qu'on a récupéré a moins de 24h
        | On passe en paramètre :
        |   - l'API Key,
        |   - le compte utilisateur,
        |   - La clé utilisateur.
        | Et on précise la version de l'API a utiliser.
        */

        # Vérification de la présence de la clé token
        $keyToken = Temp::where('key', $key_token)->first();
        # Date actuelle en UTC
        $dateNow = Carbon::now();
        # Date de la dernière modification du token
        $dateKeyToken = $keyToken->updated_at;

        # Comparaison entre les deux dates
        $resetToken = $dateNow->diffInHours($dateKeyToken);

        # Si la dernière modification date de plus de 23h
        if ($resetToken > $hours_duration_token) {
            #On récupère un nouveau token et on l'enregistre en base
            $getToken = $client->request('POST', '/login', [
                'header' => [
                    'Accept' => 'application/vnd.thetvdb.v' . $api_version,
                ],
                'json' => [
                    'apikey' => $api_key,
                    'username' => $api_username,
                    'userkey' => $api_userkey,
                ]
            ])->getBody();

            /*
            |--------------------------------------------------------------------------
            | Décodage du JSON et récupération du token dans une variable
            |--------------------------------------------------------------------------
            */
            $getToken = json_decode($getToken);

            $token = $getToken->token;
            $keyToken->value = $token;
            $keyToken->save();
        } else {
            # Sinon, on utilise celui en base
            $token = $keyToken->value;
        }

        /*
        |--------------------------------------------------------------------------
        | Récupération de la liste des mises à jour
        |--------------------------------------------------------------------------
        */
        # D'abord on récupère la date de dernière mise à jour
        $lastUpdate = Temp::where('key', 'last_update')->first();
        $lastUpdate = $lastUpdate->value;
        $nextUpdate = $lastUpdate;

        # On fait chercher la liste des dernières modifications sur TheTVDB
        $getUpdate = $client->request('GET', 'updated/query?fromTime=' . $lastUpdate, [
            'headers' => [
                'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                'Authorization' => 'Bearer ' . $token,
            ]
        ])->getBody();

        $getUpdate = json_decode($getUpdate);

        $getUpdate = $getUpdate->data;

        foreach ($getUpdate as $update) {
            $idSerie = $update->id;
            $updateSerie = $update->lastUpdated;

            if($updateSerie >= $nextUpdate) {
                $nextUpdate = $updateSerie;
            }

            # Vérification de la présence de la série dans notre BDD
            $serieInBDD = Show::where('thetvdb_id', $idSerie)->first();

            # Si la série existe
            if (!is_null($serieInBDD)) {
                Log::info('----- Modification de la série ' . $idSerie . ' -----');

                /*
                |--------------------------------------------------------------------------
                | Recupération de la série en français et avec la langue par défaut
                |--------------------------------------------------------------------------
                | Le paramètre passé est l'ID de TheTVDB passé dans le formulaire
                | On précise la version de l'API a utiliser, que l'on veut recevoir du JSON.
                | On passe également en paramètre le token.
                */
                $getShow_fr = $client->request('GET', '/series/' . $idSerie, [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                        'Accept-Language' => 'fr',
                    ]
                ])->getBody();

                $getShow_en = $client->request('GET', '/series/' . $idSerie, [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                        'Accept-Language' => 'en',
                    ]
                ])->getBody();

                /*
                |--------------------------------------------------------------------------
                | Décodage du JSON et vérification que la langue française existe sur The TVDB
                | Si la langue fr n'est pas renseignée, on met la variable languageFR à 'no'
                |--------------------------------------------------------------------------
                */
                $getShow_fr = json_decode($getShow_fr);
                $getShow_en = json_decode($getShow_en);

                $show_en = $getShow_en->data;
                $show_fr = $getShow_fr->data;

                Log::info('Nom de la série : ' . $show_en->seriesName);

                $resumeSerie = $serieInBDD->resume;
                # Si le résumé est à TBA dans notre base
                if ($resumeSerie == 'TBA') {
                    # On vérifie si le résumé est rempli en FR
                    if (!is_null($show_fr->overview)) {
                        # On sauvegarde le résumé en français
                        Log::info('Mise à jour du résumé en français.');
                        $serieInBDD->resume = $show_fr->overview;
                    } else {
                        # On vérifie que le résumé est rempli en EN
                        if (!is_null($show_en->overview)) {
                            # On sauvegarde le résumé en EN
                            Log::info('Mise à jour du résumé en anglais.');
                            $serieInBDD->resume = $show_en->overview;
                        }
                    }
                }

                $nomFRSerie = $serieInBDD->name_fr;
                # Si le nom FR est à TBA dans notre base
                if ($nomFRSerie == 'TBA') {
                    # On vérifie si le nom est rempli en FR
                    if (!is_null($show_fr->seriesName)) {
                        # On sauvegarde le nom en français
                        Log::info('Mise à jour du nom en français.');
                        $serieInBDD->name_fr = $show_fr->seriesName;
                    }
                }

                $statutSerie = $serieInBDD->encours;
                # Si le statut est à 1 dans notre base
                if ($statutSerie == '1') {
                    # On vérifie le statut sur TheTVDB
                    if ($show_en->status == 'Ended') {
                        # On enregistre le nouveau statut
                        Log::info('Mise à jour du statut.');
                        $serieInBDD->encours = 0;
                    }
                }

                $diffusionSerie = $serieInBDD->diffusion_us;
                # Si la diffusion est renseignée sur theTVDB
                if (!empty($show_en->firstAired)) {
                    # Si la diffusion dans notre BDD est égale à celle dans TheTVDB
                    if ($diffusionSerie != $show_en->firstAired) {
                        # On enregistre la nouvelle diffusion
                        Log::info('Mise à jour de la diffusion US.');
                        $serieInBDD->diffusion_us = $show_en->firstAired;
                        $dateTemp = date_create($show_en->firstAired);              # On transforme d'abord le texte récupéré par la requête en date
                        $serieInBDD->annee = date_format($dateTemp, "Y");           # Ensuite on récupère l'année
                    }
                }

                $serieInBDD->save();

                $idInBDDSerie = $serieInBDD->id;

                /*
                |--------------------------------------------------------------------------
                | Gestion des acteurs
                |--------------------------------------------------------------------------
                | On commence par récupérer les chaines du formulaire
                */
                $getActors = $client->request('GET', '/series/'. $idSerie . '/actors', [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                    ]
                ])->getBody();

                /*
                |--------------------------------------------------------------------------
                | Décodage du JSON
                |--------------------------------------------------------------------------
                */
                $actors = json_decode($getActors);
                $actors = $actors->data;

                if(!is_null($actors)) {
                    foreach ($actors as $actor) {
                        # Récupération du nom de l'acteur
                        $actorName = $actor->name;

                        # Récupération du rôle
                        $actorRole = $actor->role;
                        if (is_null($actorRole)) {
                            $actorRole = 'TBA';
                        }

                        # On supprime les espaces
                        $actor = trim($actorName);
                        # On met en forme l'URL
                        $actor_url = Str::slug($actor);
                        # Vérification de la présence de l'acteur
                        $actor_ref = Artist::where('artist_url', $actor_url)->first();

                        if(!is_null($actor_ref)) {
                            # On vérifie s'il est déjà lié à la série

                            $actor_liaison = $actor_ref->shows()
                                ->where('shows.thetvdb_id', $idSerie)
                                ->where('artistables.profession', 'actor')
                                ->get()
                                ->toArray();

                            if(empty($actor_liaison)){
                                # On lie l'acteur à la série
                                Log::info('L\'acteur ' . $actor . ' existe déjà mais n\'est pas lié à la série. On le lie.');
                                $serieInBDD->artists()->attach($actor_ref->id, ['profession' => 'actor', 'role' => $actorRole]);
                            }
                            else{
                                # On vérifie que le rôle de l'acteur est à TBA
                                $actor_role = $actor_ref->shows()
                                    ->where('shows.thetvdb_id', $idSerie)
                                    ->where('artistables.profession', 'actor')
                                    ->where('artistables.role', 'TBA')
                                    ->pluck('shows.id')
                                    ->toArray();

                                if(!empty($actor_role)){
                                    # On vérifie que le rôle est rempli sur TheTVDB
                                    if($actorRole != 'TBA'){
                                        # On met à jour le rôle
                                        Log::info('L\'acteur ' . $actor . ' est déjà lié à la série mais son rôle ' . $actorRole . ' n\'était pas rempli. On va donc modifier la ligne ' . $actor_role[0]);
                                        $test = $actor_ref->shows()->updateExistingPivot($actor_role[0], ['role' => $actorRole]);
                                        Log::info($actor_ref);
                                        Log::info($test);
                                    }
                                }
                            }
                        }
                        else{
                            # On prépare le nouvel acteur
                            $actor_ref = new Artist([
                                'name' => $actor,
                                'artist_url' => $actor_url
                            ]);

                            # Et on la sauvegarde en passant par l'objet Show pour créer le lien entre les deux
                            Log::info('L\'acteur ' . $actor . ' a été créé.');
                            $serieInBDD->artists()->save($actor_ref, ['profession' => 'actor', 'role' => $actorRole]);
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | On va chercher tous les épisodes
                |--------------------------------------------------------------------------
                */

                $getEpisodes_en = $client->request('GET', '/series/' . $idSerie .'/episodes?page=1', [
                    'headers' => [
                        'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                        'Authorization' => 'Bearer ' . $token,
                        'Accept-Language' => 'en',
                    ]
                ])->getBody();

                /*
                |--------------------------------------------------------------------------
                | Décodage du JSON
                |--------------------------------------------------------------------------
                */
                $getEpisodes_en = json_decode($getEpisodes_en);

                /*
                |--------------------------------------------------------------------------
                | Récupération des variables sur le nombre de pages du JSON de la liste des épisodes
                |--------------------------------------------------------------------------
                */
                $getEpisodeNextPage = $getEpisodes_en->links->next;
                $getEpisodeLastPage = $getEpisodes_en->links->last;
                $getEpisodes = $getEpisodes_en->data;

                /*
                |--------------------------------------------------------------------------
                | Exécution de la récupération des informations de l'épisode
                |--------------------------------------------------------------------------
                | S'il n'y a pas de Page 'Next', on se cantonne à une seule, et on execute la fonction de récupération des
                | informations.
                | S'il y a plusieurs pages, pour chaque page, on lance une nouvelle récupération des informations pour chaque
                | page et on exécute la fonction de récupération des informations.
                */
                if(is_null($getEpisodeNextPage)){
                    $this->UpdateEpisodeOneByOne($client, $getEpisodes, $api_version, $token, $serieInBDD);
                }
                else{
                    Log::info('En cours, page n°1 ');
                    $this->UpdateEpisodeOneByOne($client, $getEpisodes, $api_version, $token, $serieInBDD);

                    while($getEpisodeNextPage <= $getEpisodeLastPage) {
                        Log::info('En cours, page n°'.$getEpisodeNextPage);
                        $getEpisodes_en = $client->request('GET', '/series/' . $idSerie .'/episodes?page='. $getEpisodeNextPage, [
                            'headers' => [
                                'Accept' => 'application/json,application/vnd.thetvdb.v' . $api_version,
                                'Authorization' => 'Bearer ' . $token,
                                'Accept-Language' => 'en',
                            ]
                        ])->getBody();

                        $getEpisodes_en = json_decode($getEpisodes_en);
                        $getEpisodes = $getEpisodes_en->data;

                        $this->UpdateEpisodeOneByOne($client, $getEpisodes, $api_version, $token, $serieInBDD);
                        $getEpisodeNextPage++;
                    }
                }
            }
        }
        $newUpdate = Temp::where('key', 'last_update')->first();
        $deltaLastNext = $nextUpdate - $lastUpdate;
        if($deltaLastNext >= $secondsWeek){
            $nextUpdate = $lastUpdate + $secondsWeek;
        }

        Log::info('----- Mise à jour du timestamp -----');
        $newUpdate->value = $nextUpdate;
        $newUpdate->save();
        Log::info('>>>>>>>>>> Fin du job d\'update <<<<<<<<<<');
    }
}
