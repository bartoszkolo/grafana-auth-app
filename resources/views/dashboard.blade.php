@extends('layouts.app')

@section('content')
    <div style="position: absolute; top: 56px; left: 0; right: 0; bottom: 0; overflow: hidden;">
        <iframe src="{{ $iframeUrl }}" frameborder="0" style="height: 100%; width: 100%;"></iframe>
    </div>
@endsection
