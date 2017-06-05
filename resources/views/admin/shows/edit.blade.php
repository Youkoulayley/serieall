@extends('layouts.admin')

@section('breadcrumbs')
    <a href="{{ route('admin') }}" class="section">
        Administration
    </a>
    <i class="right angle icon divider"></i>
    <a href="{{ route('admin.shows.index') }}" class="section">
        Séries
    </a>
    <i class="right angle icon divider"></i>
    <div class="active section">
        Ajouter une série manuellement
    </div>
@endsection

@section('content')
    <h1 class="ui header" id="admin-titre">
        Modifier une série
        <span class="sub header">
            Attention, une modification de série manuelle pourrait être écrasée par une future mise à jour automatique en provenance de TheTVDB.
        </span>
    </h1>

    <form class="ui form" method="POST" action="{{ route('admin.shows.updateManually') }}">
        {{ csrf_field() }}

        <div class="ui centered grid">
            <div class="ten wide column segment">
                <div class="ui pointing secondary menu">
                    <a class="dataShow item active" data-tab="first">Série</a>
                    <a class="dataActor item" data-tab="second">Acteurs</a>
                    <a class="dataSeason item" data-tab="third">Saisons & épisodes</a>
                    <a class="dataRentree item" data-tab="fourth">Rentrée</a>
                </div>
                <div class="ui tab active" data-tab="first">
                    <div class="ui teal segment">
                        <h4 class="ui dividing header">Informations générales sur la série</h4>
                        <div class="two fields">
                            <input class="showInputID" name="id" type="hidden" value="{{ $show->id}}">
                            <div class="disabled field">
                                <label>Nom original de la série</label>
                                <input id="name" name="name" placeholder="Nom original de la série" type="text" value="{{ $show->name }}">
                                <div class="ui red hidden message"></div>
                            </div>

                            <div class="field">
                                <label>Nom français de la série</label>
                                <input id="name_fr" name="name_fr" placeholder="Nom français" type="text" value="{{ old('name_fr', $show->name_fr) }}">
                                <div class="ui red hidden message"></div>
                            </div>
                        </div>

                        <div class="two fields">
                            <div class="field">
                                <label for="resume">Résumé</label>
                                <textarea id="resume" name="resume">{{ $show->synopsis_fr }}</textarea>
                                <div class="ui red hidden message"></div>
                            </div>

                            <div class="two fields field">
                                <div class="field">
                                    <label>Format</label>
                                    <div class="ui left icon input">
                                        <input id="format" name="format" placeholder="Format de la série..." type="number" value="{{ old('format', $show->format) }}">
                                        <i class="tv icon"></i>
                                    </div>
                                    <div class="ui red hidden message"></div>
                                </div>

                                <div class="field">
                                    <label>Série en cours</label>
                                    <div id="dropdown-encours" class="ui fluid search selection dropdown">
                                        <input name="encours" type="hidden" value="{{ old('encours', $show->encours) }}">
                                        <i class="dropdown icon"></i>
                                        <span class="text">Choisir</span>
                                        <div class="menu">
                                            <div class="item" data-value="1">
                                                <i class="checkmark icon"></i>
                                                Oui
                                            </div>
                                            <div class="item" data-value="0">
                                                <i class="remove icon"></i>
                                                Non
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ui red hidden message"></div>
                                </div>
                            </div>
                        </div>

                        <div class="two fields">
                            <div class="field">
                                <label>Date de la diffusion originale</label>
                                <div class="ui calendar" id="datepicker">
                                    <div class="ui input left icon">
                                        <i class="calendar icon"></i>
                                        <input id="diffusion_us" name="diffusion_us" class="date-picker" type="date" placeholder="Date" value="{{ old('diffusion_us', $show->diffusion_us) }}">
                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>

                            <div class="field">
                                <label>Date de la diffusion française</label>
                                <div class="ui calendar" id="datepicker">
                                    <div class="ui input left icon">
                                        <i class="calendar icon"></i>
                                        <input id="diffusion_fr" name="diffusion_fr" class="date-picker" type="date" placeholder="Date" value="{{ old('diffusion_fr', $show->diffusion_fr) }}">
                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>
                        </div>

                        <div class="two fields">
                            <div class="field">
                                <label>Chaine(s)</label>
                                <div id="dropdown-chaines" class="ui fluid multiple search selection dropdown">
                                    <input id="channels" name="channels" type="hidden" value="{{ old('channels', $channels) }}">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Choisir</div>
                                    <div class="menu">
                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>

                            <div class="field">
                                <label>Nationalité(s)</label>
                                <div id="dropdown-nationalities" class="ui fluid multiple search selection dropdown">
                                    <input id="nationalities" name="nationalities" type="hidden" value="{{ old('nationalities', $nationalities) }}">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Choisir</div>
                                    <div class="menu">
                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>
                        </div>

                        <div class="two fields">
                            <div class="field">
                                <label>Créateur(s) de la série</label>
                                <div class="ui fluid multiple search selection dropdown artistsDropdown">
                                    <input id="creators" name="creators" type="hidden" value="{{ old('creators', $creators) }}">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Choisir</div>
                                    <div class="menu">

                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>

                            <div class="field">
                                <label>Genre(s)</label>
                                <div id="dropdown-genres" class="ui fluid multiple search selection dropdown">
                                    <input id="genres" name="genres" type="hidden" value="{{ old('genres', $genres) }}">
                                    <i class="dropdown icon"></i>
                                    <div class="default text">Choisir</div>
                                    <div class="menu">
                                    </div>
                                </div>
                                <div class="ui red hidden message"></div>
                            </div>
                        </div>
                        <button class="submit positive ui button" type="submit">Créer la série</button>
                    </div>
                </div>

                <div class="ui tab blue segment" data-tab="second">
                    <h4 class="ui dividing header">Ajouter un ou plusieurs acteurs</h4>
                    <p>
                        <button class="ui basic button add-actor">
                            <i class="user icon"></i>
                            Ajouter un acteur
                        </button>
                        <br />
                    </p>

                    <div class="div-actors">

                        <?php $i = 1; ?>

                        @foreach($show->actors as $actor)
                            <div class="ui segment div-actor">
                               <form action="{{ route('admin.artists.destroy', $show->id) }}" method="post" >
                                   {{ csrf_field() }}

                                   <input type="hidden" name="_method" value="DELETE">

                                   <button class="ui right floated negative basic circular icon button remove-actor-manually">
                                       <i class="remove icon"></i>
                                   </button>
                               </form>

                                <div class="two fields">

                                    <input class="actor_id-input" id="actors.{{ $i }}.id_actor" name="actors[{{ $i }}][id_actor]" type="hidden" value="{{ $actor->id }}">

                                    <div class="field">
                                        <label>Nom de l'acteur</label>
                                        <div class="ui fluid search selection dropdown artistsDropdown">
                                            <input class="actor_name-input" id="actors.{{ $i }}.name_actor" name="actors[{{ $i }}][name_actor]" type="hidden" value="{{ $actor->name }}">

                                            <i class="dropdown icon"></i>
                                            <div class="default text">Choisir</div>
                                            <div class="menu">

                                            </div>

                                        </div>

                                        <div class="ui red hidden message"></div>
                                    </div>

                                    <div class="field">
                                        <label class="actor_role-label">Rôle</label>
                                        <input class="actor_role-input" id="actors.{{ $i }}.role_actor" name="actors[{{ $i }}][role_actor]" placeholder="Rôle" type="text" value="{{ $actor->role }}">
                                        <div class="ui red hidden message"></div>

                                    </div>
                                </div>
                            </div>

                            <?php $i++; ?>
                        @endforeach

                    </div>

                    <p></p>
                    <button class="submit positive ui button" type="submit">Créer la série</button>
                </div>

                <div class="ui tab red segment" data-tab="third">
                    <h4 class="ui dividing header">Ajouter les saisons et les épisodes</h4>
                    <p>

                        <button class="ui basic button seasonAdd">
                            <i class="object group icon"></i>
                            Ajouter une saison
                        </button>
                        <br />
                    </p>

                    <div class="ui fluid styled accordion seasonsBlock" id="sortableSeasons">
                    @foreach($seasonsEpisodes as $season)

                        <div class="seasonBlock" season="{{ $season->name }}">
                            <div class="title">
                                <div class="ui grid">
                                    <div class="twelve wide column middle aligned expandableBlock seasonName">
                                        <i class="errorSeason{{ $season->name }} dropdown icon"></i>
                                        Saison {{ $season->name }}
                                        </div>
                                    <div class="four wide column">
                                        <button class="ui right floated negative basic circular icon button seasonRemove">
                                            <i class="remove icon"></i>
                                            </button>
                                        <button class="ui right floated positive basic circular icon button seasonMove">
                                            <i class="move icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <div class="content" seasonNumber="{{ $season->name }}">
                                <input class="seasonInputNumber" name="seasons[{{ $season->name }}][number]" type="hidden" value="{{ $season->name }}">

                                <div class="field">
                                    <label>Bande Annonce</label>
                                    <input class="seasonInputBA" name="seasons[{{ $season->name }}][ba]" placeholder="Bande annonce" type="text" value="{{ old('ba', $season->ba) }}">
                                    <div class="ui red hidden message"></div>
                                    </div>
                                <button class="ui basic button episodeAdd" id="episodeAdd{{ $season->name }}">
                                    <i class="tv icon"></i>
                                    Ajouter un épisode
                                    </button>
                                <div class="accordion transition hidden episodesBlock sortableEpisodes" id="episodes{{ $season->name }}">

                                    @foreach($season->episodes as $episode)
                                        <div class="episodeBlock episode{{ $season->name }}" season="{{ $season->name }}" episode="{{ $episode->numero}}">
                                            <div class="title">
                                                <div class="ui grid">
                                                    <div class="twelve wide column middle aligned expandableBlock episodeName">
                                                        <i class="errorEpisode{{ $season->name }}x{{ $episode->numero}} dropdown icon"></i>
                                                        Episode {{ $season->name }}.{{ $episode->numero }}
                                                    </div>
                                                    <div class="four wide column">
                                                        <button class="ui right floated negative basic circular icon button episodeRemove">
                                                            <i class="remove icon"></i>
                                                        </button>
                                                        <button class="ui right floated positive basic circular icon button episodeMove">
                                                            <i class="move icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="content">

                                                <input class="episodeInputNumber" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.number" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][number]" type="hidden" value="{{ $episode->numero}}">

                                                <div class="field">
                                                    <div class="ui slider checkbox">
                                                        <label for="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.special">Episode Spécial</label>
                                                        <input @if($episode->numero == 0) checked @endif class="episodeInputSpecial" onclick="clickEpisodeSpecial(this)" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.special" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][special]" type="checkbox">
                                                    </div>
                                                </div>

                                                <div class="two fields">
                                                    <div class="field">
                                                        <label>Nom original</label>
                                                        <input class="episodeInputNameEN" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.name" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][name]" placeholder="Nom original de l'épisode" type="text" value="{{ $episode->name }}">
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Nom français</label>
                                                        <input class="episodeInputNameFR" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.name_fr" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][name_fr]" placeholder="Nom français de l'épisode" type="text" value="{{ $episode->name_fr }}">
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                </div>

                                                <div class="two fields">
                                                    <div class="field">
                                                        <label>Résumé original</label>
                                                        <textarea class="episodeInputResumeEN" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.resume" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][resume]" placeholder="Résumé original de l'épisode">{{ $episode->resume }}</textarea>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Résumé de l'épisode</label>
                                                        <textarea class="episodeInputResumeFR" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.resume_fr" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][resume_fr]" placeholder="Résumé en français de l'épisode">{{ $episode->resume_fr }}</textarea>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                </div>

                                                <div class="two fields">
                                                    <div class="field">
                                                        <label>Date de la diffusion originale</label>
                                                        <div class="ui calendar" id="datepicker">
                                                            <div class="ui input left icon">
                                                                <i class="calendar icon"></i>
                                                                <input class="episodeInputDiffusionUS date-picker" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.diffusion_us" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][diffusion_us]" type="date" placeholder="Date" value="{{ $episode->diffusion_us }}">
                                                            </div>
                                                        </div>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Date de la diffusion française</label>
                                                        <div class="ui calendar" id="datepicker">
                                                            <div class="ui input left icon">
                                                                <i class="calendar icon"></i>
                                                                <input class="episodeInputDiffusionFR date-picker" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.diffusion_fr" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][diffusion_fr]" type="date" placeholder="Date" value="{{ $episode->diffusion_fr }}">
                                                            </div>
                                                        </div>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                </div>

                                                <div class="two fields">
                                                    <div class="field">
                                                        <label>Particularité</label>
                                                        <textarea rows="2" class="episodeInputParticularite" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.particularite" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][particularite]" placeholder="Particularité de l'épisode" >{{ $episode->particularite }}</textarea>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Bande annonce de l'épisode</label>
                                                        <input class="episodeInputBA" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.ba" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][ba]" placeholder="Bande Annonce de l'épisode" value="{{ $episode->ba }}">
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                </div>

                                                <div class="three fields">

                                                    <div class="field">
                                                        <label>Réalisateur(s) de l'épisode</label>
                                                        <div class="ui fluid multiple search selection dropdown artistsDropdown">
                                                            <input class="episodeInputDirectors" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.directors" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][directors]" type="hidden"
                                                                <?php $listeDirectors = "" ?>
                                                                @foreach($episode->directors as $director)
                                                                    @if ($loop->last)
                                                                        <?php $listeDirectors.= "$director->name" ; ?>
                                                                    @break
                                                                    @endif
                                                                    <?php $listeDirectors.= "$director->name," ; ?>
                                                                @endforeach
                                                                value="{{  $listeDirectors }}">
                                                            <i class="dropdown icon"></i>
                                                            <div class="default text">Choisir</div>
                                                        </div>
                                                        <div class="ui red hidden message"></div>
                                                    </div>

                                                    <div class="field">
                                                        <label>Scénariste(s) de l'épisode</label>
                                                        <div class="ui fluid multiple search selection dropdown artistsDropdown">
                                                            <input class="episodeInputWriters" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.writers" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][writers]" type="hidden"
                                                                <?php $listeWriters = "" ?>
                                                                @foreach($episode->writers as $writer)
                                                                    @if ($loop->last)
                                                                        <?php $listeWriters.= "$writer->name" ; ?>
                                                                        @break
                                                                    @endif
                                                                    <?php $listeWriters.= "$writer->name," ; ?>
                                                                @endforeach
                                                                value="{{ $listeWriters }}">
                                                            <i class="dropdown icon"></i>
                                                            <div class="default text">Choisir</div>
                                                        </div>
                                                        <div class="ui red hidden message"></div>
                                                    </div>

                                                    <div class="field">
                                                        <label>Guest(s) de l'épisode</label>
                                                        <div class="ui fluid multiple search selection dropdown artistsDropdown">
                                                            <input class="episodeInputGuests" id="seasons.{{ $season->name }}.episodes.{{ $episode->numero}}.guests" name="seasons[{{ $season->name }}][episodes][{{ $episode->numero}}][guests]" type="hidden"
                                                                <?php $listeGuests = "" ?>
                                                                @foreach($episode->guests as $guest)
                                                                    @if ($loop->last)
                                                                        <?php $listeGuests.= "$guest->name" ; ?>
                                                                        @break
                                                                    @endif
                                                                    <?php $listeGuests.= "$guest->name," ; ?>
                                                                @endforeach
                                                                <?php echo $listeGuests; ?>
                                                                value="{{ $listeGuests }}">
                                                            <i class="dropdown icon"></i>
                                                            <div class="default text">Choisir</div>

                                                        </div>
                                                        <div class="ui red hidden message"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach





                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <p></p>
                    <button class="submit positive ui button" type="submit">Créer la série</button>
                </div>

                <div class="ui tab violet segment" data-tab="fourth">
                    <h4 class="ui dividing header">Informations sur la rentrée</h4>
                    <div class="two fields">
                        <div class="field">
                            <label>Taux érectile</label>
                            <div class="ui left icon input">
                                <input id="taux_erectile" name="taux_erectile" placeholder="Pourcentage..." type="number" value="{{ old('taux_erectile', $show->taux_erectile) }}">
                                <i class="percent icon"></i>
                            </div>
                            <div class="ui red hidden message"></div>
                        </div>

                        <div class="field">
                            <label for="avis_rentree">Avis de la rédaction</label>
                            <textarea id="avis_rentree" name="avis_rentree">{{ $show->avis_rentree }}</textarea>
                            <div class="ui red hidden message"></div>
                        </div>
                    </div>
                    <button class="submit positive ui button" type="submit">Créer la série</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>

        $('.artistsDropdown')
            .dropdown({
                apiSettings: {
                    url: '/api/artists/list?name-lk=*{query}*'
                },
                fields: {remoteValues: "data", value: "name"},
                allowAdditions: true,
                forceSelection : false,
                minCharacters: 2
            });

        $('#dropdown-genres')
                .dropdown({
                    apiSettings: {
                        url: '/api/genres/list?name-lk=*{query}*'
                    },
                    fields: {remoteValues: "data", value: "name"},
                    allowAdditions: true,
                    forceSelection : false
                })
        ;
        $('#dropdown-chaines')
                .dropdown({
                    apiSettings: {
                        url: '/api/channels/list?name-lk=*{query}*'
                    },
                    fields: {remoteValues: "data", value: "name"},
                    allowAdditions: true,
                    forceSelection : false
                })
        ;

        $('#dropdown-nationalities')
                .dropdown({
                    apiSettings: {
                        url: '/api/nationalities/list?name-lk=*{query}*'
                    },
                    fields: {remoteValues: "data", value: "name"},
                    allowAdditions: true,
                    forceSelection : false
                })
        ;

        $( '.date-picker' ).datepicker({
            showAnim: "blind",
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true
        });

        $('#dropdown-encours')
                .dropdown({
                })
        ;

        $('.menu .item')
                .tab()
        ;

        $('.ui.styled.fluid.accordion.seasonsBlock')
                .accordion({
                    selector: {
                        trigger: '.expandableBlock'

                    },
                    exclusive: false
                })
        ;

        // Fonction de création et de suppression des nouveau acteurs
        $(function(){
            // Définition des variables
            var max_fields  =   50; // Nombre maximums de ligne sautorisées
            var actor_number  =  $('.div-actors').length; // Nombre d'acteurs

            // Suppression d'un acteur
            $(document).on('click', '.remove-actor', function(){
                $(this).parents('.div-actor').remove();
                $(this).find(".actor_name-input").attr( "name", 'actors[' + index + '][name]');
                $(this).find(".actor_role-input").attr( "name", 'actors[' + index + '[role]');
                $(this).find(".actor_name-input").attr( "id", 'actors.' + index + '.name');
                $(this).find(".actor_role-input").attr( "id", 'actors.' + index + '.role');
            });

            // Ajouter un acteur
            $('.add-actor').click(function(e) {
                e.preventDefault();

                if (actor_number < max_fields) {
                    var html = '<div class="ui segment div-actor">'
                        + '<button class="ui right floated negative basic circular icon button remove-actor">'
                        + '<i class="remove icon"></i>'
                        + '</button>'
                        + '<div class="two fields">'

                        + '<div class="field">'
                        + '<label>Nom de l\'acteur</label>'
                        + '<div class="ui fluid search selection dropdown actorDropdown">'
                        + '<input class="actor_name-input" id="actors.'+ actor_number +'.name_actor" name="actors[' + actor_number + '][name_actor]" type="hidden" value="{{ old('guests') }}">'
                        + '<i class="dropdown icon"></i>'
                        + '<div class="default text">Choisir</div>'
                        + '<div class="menu">'
                        + '</div>'
                        + '</div>'
                        + '<div class="ui red hidden message"></div>'
                        + '</div>'

                        + '<div class="field">'
                        + '<label class="actor_role-label">Rôle</label>'
                        + '<input class="actor_role-input" id="actors.'+ actor_number +'.role_actor" name="actors[' + actor_number + '][role_actor]" placeholder="Rôle" type="text" value="{{ old('role_actor') }}">'
                        + '<div class="ui red hidden message"></div>'

                        + '</div>'
                        + '</div>'
                        + '</div>';

                    $(function() {
                        $('.actorDropdown')
                            .dropdown({
                                apiSettings: {
                                    url: '/api/artists/list?name-lk=*{query}*'
                                },
                                fields: {remoteValues: "data", value: "name"},
                                allowAdditions: true,
                                forceSelection : false,
                                minCharacters: 4
                            });
                    });

                    ++actor_number;

                    $('.div-actors').append(html);
                }
            });
        });

        // Submission
        $(document).on('submit', 'form', function(e) {
            e.preventDefault();

            $('.submit').addClass("loading");

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json"
            })
                    .done(function () {
                        window.location.href = '{!! route('admin.shows.redirectJSON') !!}';
                    })
                    .fail(function (data) {
                        $('.submit').removeClass("loading");

                        $.each(data.responseJSON, function (key, value) {
                            var input = 'input[id="' + key + '"]';

                            $(input + '+div').text(value);
                            $(input + '+div').removeClass("hidden");
                            $(input).parent().addClass('error');

                            if(key.indexOf('actors.') > -1) {
                                $(input).parents('.div-actor').addClass('red');

                                var dataActor = $('.dataActor');

                                $(dataActor).addClass('red');
                                $(dataActor).css('color', '#DB3041');
                            }
                            else if(key.indexOf('seasons.') > -1) {
                                var seasonNumber = $(input).parents('.episodeBlock').attr('season');
                                var episodeNumber = $(input).parents('.episodeBlock').attr('episode');

                                $('.errorSeason' + seasonNumber).addClass('red');
                                $('.errorEpisode' + seasonNumber + 'x' + episodeNumber).addClass('red');

                                var dataSeason = $('.dataSeason');

                                $(dataSeason).addClass('red');
                                $(dataSeason).css('color', '#DB3041');
                            }
                            else if(key.indexOf('taux_erectile') > -1) {
                                var dataRentree = $('.dataRentree');

                                $(dataRentree).addClass('red');
                                $(dataRentree).css('color', '#DB3041');
                            }
                            else{
                                var dataShow = $('.dataShow');
                                $(dataShow).addClass('red');
                                $(dataShow).css('color', '#DB3041');
                            }
                        });
                    });
        });
    </script>
@endsection