@extends('layouts.fiche')

@section('pageTitle', 'S' . $seasonInfo->name . ' - ' . $showInfo['show']->name)

@section('menu_fiche')
    <div id="menuListSeasons" class="row">
        <div class="column ficheContainer">
            <div class="ui segment">
                <div class="ui stackable secondary menu">
                    <div id="seasonsLine" class="ui stackable grid">
                        @foreach($showInfo['show']->seasons as $season)
                            <a class="
                                @if($seasonInfo->name == $season->name)
                                    active
                                @endif
                                    item" href="{{ route('season.fiche', [$showInfo['show']->show_url, $season->name]) }}">Saison {{ $season->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content_fiche_left')
    <div class="ui stackable grid">
        <div class="row">
            <div id="ListSeasons" class="ui segment">
                <h1>Liste des épisodes</h1>
                    <table class="ui padded table center aligned">
                        @foreach($seasonInfo->episodes as $episode)
                            <tr>
                                <td class="left aligned">
                                    {!! affichageNumeroEpisode($showInfo['show']->show_url, $seasonInfo->name, $episode->numero, $episode->id, true, true) !!}
                                </td>
                                <td class="left aligned">
                                    @if(!empty($episode->name_fr))
                                        {{ $episode->name_fr }}
                                    @else
                                        {{ $episode->name }}
                                    @endif
                                </td>
                                <td>
                                    @if($episode->diffusion_us != "0000-00-00")
                                        {!! formatDate('long', $episode->diffusion_us) !!}
                                    @else
                                        <span class="ui grey text">Pas de date</span>
                                    @endif
                                </td>
                                <td>
                                    @if($episode->moyenne < 1)
                                        <p class="ui black text">
                                            -
                                        </p>
                                    @else
                                        {!! affichageNote($episode->moyenne) !!}
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
                            </tr>
                        @endforeach
                    </table>
            </div>
        </div>

        <div class="row">
            <div class="chartMean column">
                {!! $chart->html() !!}
            </div>
        </div>

        <div class="row">
            @include('comments.avis_fiche')
        </div>
    </div>
@endsection

@section('content_fiche_right')
    <div class="ui stackable grid">
        <div class="row">
            <div class="ui segment center aligned">
                @if($seasonInfo->moyenne < 1)
                    <p class="NoteSeason">
                        -
                    </p>
                    <p>
                        Pas encore de notes
                    </p>
                @else
                    @if($seasonInfo->moyenne >= $noteGood)
                        <p class="NoteSeason ui green text">
                    @elseif($seasonInfo->moyenne >= $noteNeutral && $seasonInfo->moyenne < $noteGood)
                        <p class="NoteSeason ui gray text">
                    @else
                        <p class="NoteSeason ui red text">
                    @endif
                            {{ $seasonInfo->moyenne }}
                    </p>
                    <p>
                        {{ $seasonInfo->nbnotes }}
                        @if($seasonInfo->nbnotes <= 1)
                            note
                        @else
                            notes
                        @endif
                    </p>
                @endif

                <div class="ui divider"></div>

                <div class="ui feed showMoreOrLess">
                    @foreach($ratesSeason['users'] as $rate)
                        <div class="event">
                            <div class="label">
                                <img src="{{ Gravatar::src($rate['user']['email']) }}">
                            </div>
                            <div class="content">
                                <div class="summary">
                                    <a href="{{ route('user.profile', $rate['user']['username']) }}" class="user">
                                        {{ $rate['user']['username'] }}
                                    </a>
                                    a noté {!! affichageNumeroEpisode($showInfo['show']->show_url, $seasonInfo->name, $rate['episode']['numero'], $rate['episode']['id'], true, false) !!} - {!! affichageNote($rate['rate']) !!}

                                    <div class="date">{!! formatDate('short', $rate['updated_at']) !!}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="fadeDiv fadeShowMoreOrLess"></div>
                <div><button class="ui button slideShowMoreOrLess">Voir plus</button></div>
            </div>
        </div>

        <div class="row">
            <div id="LastArticles" class="ui segment">
                <h1>Derniers articles sur la saison</h1>
                <div class="ui stackable grid">
                    <div class="row">
                        <div class="center aligned four wide column">
                            <img src="{!! ShowPicture($showInfo['show']->show_url) !!}" />
                        </div>
                        <div class="eleven wide column">
                            <a><h2>Critique 01.03</h2></a>
                            <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="center aligned four wide column">
                            <img src="{!! ShowPicture($showInfo['show']->show_url) !!}" />
                        </div>
                        <div class="eleven wide column">
                            <a><h2>Critique 01.02</h2></a>
                            <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="center aligned four wide column">
                            <img src="{!! ShowPicture($showInfo['show']->show_url) !!}" />
                        </div>
                        <div class="eleven wide column">
                            <a><h2>Critique 01.01</h2></a>
                            <p class="ResumeArticle">Ceci est une critique test, et on parle et on parle, tout ça pour faire des vues, nianiania...</p>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <button class="ui right floated button">
                        Tous les articles
                        <i class="right arrow icon"></i>
                    </button>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var $divView = $('.showMoreOrLess');
            var innerHeight = $divView.removeClass('showMoreOrLess').height();
            $divView.addClass('showMoreOrLess');

            if(innerHeight < 220) {
                $('.fadeShowMoreOrLess').remove();
                $('.slideShowMoreOrLess').remove();
                $divView.removeClass('showMoreOrLess');
            }

            $('.slideShowMoreOrLess').click(function() {
                $('.showMoreOrLess').animate({
                    height: (($divView.height() == 220)? innerHeight  : "220px")
                }, 500);

                if($divView.height() == 220) {
                    $('.slideShowMoreOrLess').text('Voir moins');
                    $('.fadeDiv').removeClass('fadeShowMoreOrLess');
                }
                else {
                    $('.slideShowMoreOrLess').text('Voir plus');
                    $('.fadeDiv').addClass('fadeShowMoreOrLess');
                }
                return false;
            });
        });
    </script>
@endsection

{!! Charts::scripts() !!}
{!! $chart->script() !!}
