@extends('layouts.admin')

@section('breadcrumbs')
    <a href="{{ route('adminIndex') }}" class="section">
        Administration
    </a>
    <i class="right angle icon divider"></i>
    <a href="{{ route('adminShow.index') }}" class="section">
        Séries
    </a>
    <i class="right angle icon divider"></i>
    <div class="active section">
        Ajouter une série
    </div>
@endsection

@section('content')
    <h1 class="ui header" id="admin-titre">
        Ajouter une série
        <div class="sub header">
            Remplir le formulaire ci-dessous pour ajouter une nouvelle série
        </div>
    </h1>
    <div class="ui centered grid">
        <div class="ten wide column segment">
            <form class="ui form" method="POST" action="{{ route('adminShow.store') }}">
                {{ csrf_field() }}

                @if (session('status'))
                    <div class="ui success message">
                        <i class="close icon"></i>
                        <div class="header">
                            {{ session('status_header') }}
                        </div>
                        <p>{{ session('status_message') }}</p>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="ui warning message">
                        <i class="close icon"></i>
                        <div class="header">
                            {{ session('warning_header') }}
                        </div>
                        <p>{{ session('warning_message') }}</p>
                    </div>
                @endif

                <div class="field {{ $errors->has('thetvdb_id') ? ' error' : '' }}">
                    <label>ID de la série sur TheTVDB</label>
                    <input name="thetvdb_id" placeholder="TheTVDB ID" type="text" value="{{ old('thetvdb_id') }}">

                    @if ($errors->has('thetvdb_id'))
                        <div class="ui red message">
                            <strong>{{ $errors->first('thetvdb_id') }}</strong>
                        </div>
                    @endif
                </div>


                <div class="field {{ $errors->has('creators') ? ' error' : '' }}">
                    <label>Créateur(s) de la série</label>
                    <div id="dropdown-creators" class="ui fluid multiple search selection dropdown">
                        <input name="creators" type="hidden">
                        <i class="dropdown icon"></i>
                        <div class="default text">Choisir</div>
                        <div class="menu">
                            @foreach($artists as $artist)
                                <div class="item" data-value="{{ $artist->name }}">{{ $artist->name }}</div>
                            @endforeach
                        </div>
                    </div>

                    @if ($errors->has('creators'))
                        <div class="ui red message">
                            <strong>{{ $errors->first('creators') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="field {{ $errors->has('genres') ? ' error' : '' }}">
                    <label>Genres</label>
                    <div id="dropdown-genres" class="ui fluid multiple search selection dropdown">
                        <input name="genres" type="hidden">
                        <i class="dropdown icon"></i>
                        <div class="default text">Choisir</div>
                        <div class="menu">
                            @foreach($genres as $genre)
                                <div class="item" data-value="{{ $genre->name }}">{{ $genre->name }}</div>
                            @endforeach
                        </div>
                    </div>

                    @if ($errors->has('genres'))
                        <div class="ui red message">
                            <strong>{{ $errors->first('genres') }}</strong>
                        </div>
                    @endif
                </div>

                <button class="positive ui button" type="submit">Créer la série</button>
            </form>
        </div>
    </div>

    @section('scripts')
        $('.message .close')
        .on('click', function() {
            $(this)
            .closest('.message')
            .transition('fade')
            ;
        })
        ;

        $('#dropdown-creators')
        .dropdown({
            allowAdditions: true
        })
        ;
    @endsection


@endsection