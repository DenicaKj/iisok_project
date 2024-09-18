@extends('layouts.app')

@section('content')
    <div class="container">
    <h1 class="text-center mb-5">Article Details</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $article->title }}</h5>
            <p class="card-text">{{ $article->content }}</p>
            <p class="badge badge-primary">{{ $article->source }}</p>
            <a href="{{ $article->url }}" class="btn btn-primary">Read More</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Sentiment Analysis</h4>
        </div>
        <div class="card-body">
            <h5 class="d-flex align-items-center">
                <i class="bi {{ $sentiment['label'] == 'POSITIVE' ? 'bi-emoji-smile text-success' : ($sentiment['label'] == 'NEGATIVE' ? 'bi-emoji-frown text-danger' : 'bi-emoji-neutral text-warning') }} me-2"></i>
                Sentiment: <span class="{{ $sentiment['label'] == 'POSITIVE' ? 'text-success' : ($sentiment['label'] == 'NEGATIVE' ? 'text-danger' : 'text-warning') }} ms-2">{{ $sentiment['label'] }}</span>
            </h5>
            <p class="mb-0">Score: <strong>{{ number_format($sentiment['score'] * 100, 2) }}%</strong></p>
        </div>
    </div>

    <h2>Top 10% Similar Articles</h2>
    <div class="row">
        @foreach($similarArticles as $similarArticle)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $similarArticle['article']->title }}</h5>
                        <p class="card-text">{{ Str::limit($similarArticle['article']->content, 200) }}</p>
                        <p class="badge badge-primary">Similarity: {{ $similarArticle['similarity'] }}%</p>
                        <a href="{{ $similarArticle['article']->url }}" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
