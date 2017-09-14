@extends('layouts.fiche')

@section('menu_fiche')
    <div id="menuFiche" class="menuFiche row">
        <div class="column">
            <div class="ui fluid six item stackable menu ficheContainer">
                <a class="active item">
                    <i class="big home icon"></i>
                    Présentation
                </a>
                <a class="item" href="{{ route('show.seasons', [$showInfo['show']->show_url, '1']) }}">
                    <i class="big browser icon"></i>
                    Saisons
                </a>
                <a class="item" href="{{ route('show.details', $showInfo['show']->show_url) }}">
                    <i class="big list icon"></i>
                    Informations détaillées
                </a>
                <a class="item">
                    <i class="big comments icon"></i>
                    Avis
                </a>
                <a class="item">
                    <i class="big write icon"></i>
                    Articles
                </a>
                <a class="item">
                    <i class="big line chart icon"></i>
                    Statistiques
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content_fiche_left')
     <div class="ui stackable grid">
         <div class="row">
             <div id="ListSeasons" class="ui segment">
                 <h1>Liste des saisons</h1>
                 <table class="ui padded table center aligned">
                     @foreach($showInfo['seasons'] as $season)
                         <tr>
                             <td>
                                 <a href="{{ route('show.seasons', [$showInfo['show']->show_url, $season->name]) }}">Saison {{ $season->name }}</a>
                             </td>
                             <td>
                                 @if($season->moyenne < 1)
                                     -
                                 @else
                                     {!! affichageNote($season->moyenne) !!}
                                 @endif

                             </td>
                             <td>
                                 24
                                 <i class="green smile large icon"></i>

                                 12
                                 <i class="grey meh large icon"></i>

                                 3
                                 <i class="red frown large icon"></i>
                             </td>
                             <td>
                                 {{ $season->episodes_count }}
                                 @if($season->episodes_count == 1)
                                     épisode
                                 @else
                                     épisodes
                                 @endif
                             </td>
                         </tr>
                     @endforeach
                 </table>
                 <a href="{{ route('show.seasons', [$showInfo['show']->show_url, '1']) }}"><p class="AllSeasons">Toutes les saisons ></p></a>
             </div>
         </div>
         <div class="row">
             <div id="ListAvis" class="ui segment">
                 <h1>Derniers avis sur la série</h1>
                 <div class="ui stackable grid">
                     @foreach($last_avis as $avis)
                         <div class="row">
                             <div class="center aligned three wide column">
                                 <a href="{{ route('user.profile', $avis->user->username) }}"><img class="ui tiny avatar image" src="{{ Gravatar::src($avis->user->email) }}">
                                 {{ $avis->user->username }}</a>
                                 <br />
                                 {!! roleUser($avis->user->role) !!}
                             </div>
                             <div class="AvisBox center aligned twelve wide column">
                                <table class="ui {!! affichageThumbBorder($avis->thumb) !!} left border table">
                                    <tr>
                                        {!! affichageThumb($avis->thumb) !!}
                                        <td class="right aligned">Déposé le {{ $avis->created_at }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="AvisResume">
                                            {!! $avis->message !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ui grey text">{{--Réponse--}}</td>
                                        <td class="LireAvis"><a>Lire l'avis complet ></a></td>
                                    </tr>
                                </table>
                             </div>
                         </div>
                     @endforeach
                     <div class="row">
                         <div class="three wide column">

                         </div>
                         <div class="twelve wide column">
                             @if(Auth::check())
                                 <div class="ui DarkBlueSerieAll button WriteAvis">
                                     <i class="write icon"></i>
                                     @if(is_null($avis_user))
                                         Ecrire un avis
                                     @else
                                         Modifier mon avis
                                     @endif

                                 </div>
                                 <a class="AllAvis" href="#"><p>Toutes les avis ></p></a>


                                 <div class="ui modal">

                                     <div class="header">
                                         @if(is_null($avis_user))
                                             Ecrire un avis sur la série
                                         @else
                                             Modifier mon avis sur la série
                                         @endif

                                     </div>
                                     <div class="content">
                                         <form id="formAvis" class="ui form" method="post" action="{{ route('comment.store') }}">
                                            {{ csrf_field() }}

                                             <input type="hidden" name="show_id" value="{{ $showInfo['show']->id }}">

                                             <div class="ui field">
                                                 <div class="textarea input">
                                                     <textarea name="avis" id="avis" class="avis" placeholder="Ecrivez votre avis ici...">
                                                         @if(!is_null($avis_user))
                                                            {{ $avis_user->message }}
                                                         @endif
                                                     </textarea>

                                                     <div class="nombreCarac ui red hidden message">
                                                        100 caractères minimum requis.
                                                     </div>
                                                 </div>

                                             </div>

                                             <div class="ui field">
                                                 <div class="ui fluid selection dropdown">
                                                     <input name="thumb" id="thumb" class="thumb" type="hidden" value="@if(!is_null($avis_user)){{ $avis_user->thumb }}@endif">
                                                     <i class="dropdown icon"></i>
                                                     <div class="default text">Choisissez un type</div>
                                                     <div class="menu">
                                                         <div class="item" data-value="1">
                                                             <i class="green smile large icon"></i>
                                                             Avis favorable
                                                         </div>
                                                         <div class="item" data-value="2">
                                                             <i class="grey meh large icon"></i>
                                                             Avis neutre
                                                         </div>
                                                         <div class="item" data-value="3">
                                                             <i class="red frown large icon"></i>
                                                             Avis défavorable
                                                         </div>
                                                     </div>
                                                 </div>
                                                 <div class="ui red hidden message"></div>
                                             </div>

                                             <p></p>

                                             <button class="ui submit positive button">Envoyer</button>
                                         </form>
                                         <script>
                                            CKEDITOR.replace( 'avis' , {wordcount: { showCharCount: true}} );
                                         </script>
                                     </div>
                                 </div>
                             @endif
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
@endsection

@section('content_fiche_right')
     <div class="ui stackable grid">
         <div class="row">
             <div id="ButtonsActions">
                 <div class="ui segment">
                     <div class="ui fluid icon dropdown DarkBlueSerieAll button">
                         <span class="text"><i class="tv icon"></i>Actions sur la série</span>
                         <div class="menu">
                             <div class="item">
                                 <i class="play icon"></i>
                                 Je regarde la série
                             </div>
                             <div class="item">
                                 <i class="pause icon"></i>
                                 Je mets en pause la série
                             </div>
                             <div class="item">
                                 <i class="stop icon"></i>
                                 J'abandonne la série
                             </div>
                         </div>
                     </div>
                     <button class="ui fluid button">
                         <i class="calendar icon"></i>
                         J'ajoute la série dans mon planning
                     </button>
                 </div>
             </div>
         </div>
         <div class="row">
             <div id="LastArticles" class="ui segment">
                 <h1>Derniers articles sur la série</h1>
                 <div class="ui stackable grid">
                     <div class="row">
                         <div class="center aligned four wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                         </div>
                         <div class="eleven wide column">
                             <a><h2>Critique 01.03</h2></a>
                             <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                         </div>
                     </div>
                     <div class="row">
                         <div class="center aligned four wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                         </div>
                         <div class="eleven wide column">
                             <a><h2>Critique 01.02</h2></a>
                             <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                         </div>
                     </div>
                     <div class="row">
                         <div class="center aligned four wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                         </div>
                         <div class="eleven wide column">
                             <a><h2>Critique 01.01</h2></a>
                             <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                         </div>
                     </div>
                 </div>
                 <a href="#"><p class="AllArticles">Tous les articles ></p></a>
             </div>
         </div>
         <div class="row">
             <div id="SimilarShows" class="ui segment">
                 <h1>Séries similaires</h1>
                 <div class="ui center aligned stackable grid">
                     <div class="row">
                         <div class="center aligned five wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                             <span>Série 1</span>
                         </div>
                         <div class="center aligned five wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                             <span>Série 2</span>
                         </div>
                         <div class="center aligned five wide column">
                             <img src="{{ $folderShows }}/{{ $showInfo['show']->show_url }}.jpg" alt="Affiche {{ $showInfo['show']->name }}" />
                             <span>Série 3</span>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
@endsection

@section('scripts')
    <script>
        $('.ui.modal').modal('attach events', '.ui.button.WriteAvis', 'show');
        $('.ui.fluid.selection.dropdown').dropdown({forceSelection: true});
        $('.ui.accordion').accordion({});
        $('.spoiler').spoiler();

        // Submission
        $(document).on('submit', '#formAvis', function(e) {
            e.preventDefault();

            var messageLength = CKEDITOR.instances['avis'].getData().replace(/<[^>]*>|\n|&nbsp;/g, '').length;
            var nombreCaracAvis = '{!! config('param.nombreCaracAvis') !!}';

            if(messageLength < nombreCaracAvis ) {
                $('.nombreCarac').removeClass("hidden");
            }
            else {
                $('.submit').addClass("loading");

                var divNestedSpoilers = "div.content>div.accordion";
                $(divNestedSpoilers).removeClass("ui fluid styled");
                var message = CKEDITOR.instances['avis'].getData();

                console.log(message);

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    dataType: "json"
                })
                    .done(function () {
                        window.location.reload(false);
                    })
                    .fail(function (data) {
                        $('.submit').removeClass("loading");

                        $.each(data.responseJSON, function (key, value) {
                            var input = 'input[class="' + key + '"]';

                            $(input + '+div').text(value);
                            $(input + '+div').removeClass("hidden");
                            $(input).parent().addClass('error');
                        });
                    });
            }
        });
    </script>
@endsection