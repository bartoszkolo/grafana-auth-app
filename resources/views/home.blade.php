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
                        @foreach ($userDashboards as $dashboard)
                            <?php
                                $dashboardUrl = app('App\Services\GrafanaService')->getDashboardIframeUrl(auth()->user()->api_token, $dashboard['uid']);
                            ?>
                            <li>
                                <a href="{{ $dashboardUrl }}" target="_blank">{{ $dashboard['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
