@extends('layouts.admin')

@section('pageTitle', 'Admin - Logs')

@section('breadcrumbs')
    <a href="{{ route('admin') }}" class="section">
        Administration
    </a>
    <i class="right angle icon divider"></i>
    <a href="{{ route('admin.system') }}" class="section">
        Système
    </a>
    <i class="right angle icon divider"></i>
    <div class="active section">
        Logs
    </div>
@endsection

@section('content')
    <div>
        <div class="ui grid">
            <div class="ui height wide column">
                <h1 class="ui header" id="adminTitre">
                    Logs
                    <span class="sub header">
                        Liste de tous les logs de Série-All
                    </span>
                </h1>
            </div>
        </div>

        <table id="tableAdmin" class="ui sortable selectable celled table">
            <thead>
                <tr>
                    <th>Nom de l'action</th>
                    <th>Objet modifié</th>
                    <th>ID de l'objet</th>
                    <th>Utilisateur</th>
                    <th>Date</th>
                </tr>
            </thead>
            @foreach($logs as $log)
                <tr>
                    <td><a href="{{ route('admin.logs.view', $log->id) }}">{{ $log->job }}</a></td>
                    <td>{{ $log->object }}</td>
                    <td>
                        @if($log->object_id != 0)
                            {{$log->object_id }}
                        @else
                            All
                        @endif
                    </td>
                    <td>
                        @if(!is_null($log->user))
                            {{ $log->user->username }}
                        @else
                            System
                        @endif
                    </td>
                    <td>{{ $log->created_at }}</td>
                </tr>
            @endforeach
        </table>
    </div>

        <script>
            $('#tableAdmin').DataTable( {
                "order": [[ 4, "desc" ]],
                "language": {
                    "lengthMenu": "Afficher _MENU_ enregistrements par page",
                    "zeroRecords": "Aucun enregistrement trouvé",
                    "info": "Page _PAGE_ sur _PAGES_",
                    "infoEmpty": "Aucun enregistrement trouvé",
                    "infoFiltered": "(filtré sur _MAX_ enregistrements)",
                    "sSearch" : "",
                    "oPaginate": {
                        "sFirst":    	"Début",
                        "sPrevious": 	"Précédent",
                        "sNext":     	"Suivant",
                        "sLast":     	"Fin"
                    }
                }} );

            $('#add-serie')
                    .on('click', function() {
                        $('.ui.modal')
                                .modal({
                                    inverted: true
                                })
                                .modal('show')
                        ;
                    })
            ;
        </script>
@endsection

