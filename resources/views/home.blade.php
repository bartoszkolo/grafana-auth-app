@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
                
                <div class="card-body">
                    <h3>Your Dashboards:</h3>
                    <ul>
                        @foreach($userDashboards as $dashboard)
                            @if ($dashboard['type'] !== 'dash-folder')
                                <li>
                                    <a href="{{ route('dashboard.show', ['dashboardUid' => $dashboard['uid']]) }}">{{ $dashboard['title'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
