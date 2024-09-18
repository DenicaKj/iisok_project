@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-5">My Bookmarked Articles</h1>

        @if($bookmarks->count())
            <div class="row">
                @foreach($bookmarks as $article)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $article->title }}</h5>
                                <p class="card-text">{{ Str::limit($article->content, 200) }}</p>
                                <a href="{{ $article->url }}" class="btn btn-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No bookmarked articles found.</p>
        @endif
    </div>
@endsection
