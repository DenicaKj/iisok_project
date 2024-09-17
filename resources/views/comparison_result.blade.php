@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-5">Comparison Result</h1>

        @if(isset($article1) && isset($article2))
            <h3>Comparison between "{{ $article1->title }}" and "{{ $article2->title }}"</h3>
        @else
            <h3>Comparison between custom texts</h3>
        @endif

        <p class="mt-4"><strong>Similarity Score: </strong>{{ $similarity }}%</p>

        <a href="{{ route('compare.form') }}" class="btn btn-primary mt-3">Go Back</a>
    </div>
@endsection
