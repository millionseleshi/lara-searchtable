<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

</head>
<body class="antialiased">
<div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">


    {!! Form::open(['method' => 'GET', 'url' => '/search'])  !!}


    <input type="text" class="engine responsive" name="search" placeholder="Search here... "
           value="{{ request('search') }}">
    <span class="input-group-append">
                                    <button type="submit" class="btn btn-info btn-just-icon">
                                    <i class="material-icons">search</i>
                                    <div class="ripple-container"></div>
                                    </button>
                                </span>

    <br>
    @if(isset($response))
        @foreach($response['result'] as  $searchResult)
            <br>
            {{$searchResult}}
            <br>

        @endforeach
        {{$response['result']->links()}}
    @endif

    @error('search')
    <div class="alert alert-danger  alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong> <br>
    </div>
    @enderror


    {!! Form::close() !!}

</div>
</body>
</html>
