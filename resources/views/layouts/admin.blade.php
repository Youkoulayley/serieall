<!DOCTYPE html>
<html lang="en" id="html-admin">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SérieAll BETA</title>
    <link rel="icon" href="images/logo_v2.ico">

    <!-- CSS -->
    {{ Html::style('/semantic/semantic.css') }}
    {{ Html::style('/semantic/semantic_perso.css') }}
    {{ Html::style('/css/font-awesome.css') }}

    <!-- Javascript -->
    {{ Html::script('/js/jquery.js') }}
    {{ Html::script('/js/datatables.js') }}
    {{ Html::script('/semantic/semantic.js') }}

</head>
<body>
    <div class="ui stackable menu">
        <a class="item click-sidebar">
            <i class="fa fa-bars"></i>
        </a>
        <div class="ui breadcrumb item">
            <a class="section">Administration</a>
            <i class="fa fa-chevron-right icon"></i>
            <a class="active section">Séries</a>
        </div>
    </div>
    <div class="ui bottom attached segment pushable">
        <div class="ui visible inverted left vertical sidebar menu">
            <a class="item">
                <img src="/images/logo_v2.png">
            </a>
            <a class="item">
                <i class="fa fa-television"></i>
                Séries
            </a>
            <a class="item">
                <i class="fa fa-file-o"></i>
                Articles
            </a>
            <a class="item">
                <i class="fa fa-users"></i>
                Utilisateurs
            </a>
            <a class="item">
                <i class="fa fa-cogs"></i>
                Système
            </a>
        </div>
        <div class="pusher">
            <div class="ui basic segment">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        @yield('scripts')

        $('.dropdown')
                .dropdown()
        ;

        $('.ui.sidebar')
                .sidebar({
                    dimPage: false,
                    closable: false
                })
                .sidebar('attach events', '.menu .click-sidebar')
        ;
    </script>

</body>
</html>