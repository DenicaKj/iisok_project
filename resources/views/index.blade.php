@extends('layouts.app')

@section('content')
    <h1 class="text-center mb-5">Stored Articles</h1>

    @if($storedArticles->count())
        <div class="row">
            @foreach($storedArticles as $article)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $article->title }}</h5>
                            <p class="card-text">{{ Str::limit($article->content, 200) }}</p>
                            <p class="badge badge-primary">{{ $article->source }}</p>
                            <a href="{{ $article->url }}" class="btn btn-primary">Read More</a>
                            <a href="{{ route('details', $article->id) }}" class="btn btn-secondary float-right">Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No stored articles found.</p>
    @endif
@endsection
