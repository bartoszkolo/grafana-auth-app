@extends('layouts.app')

@section('content')
<style>
    .iframe-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        overflow: hidden;
    }
    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>
<div class="iframe-container">
    <iframe src="{{ $iframeUrl }}" frameborder="0"></iframe>
</div>
@endsection
