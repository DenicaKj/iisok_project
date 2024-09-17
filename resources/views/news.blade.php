@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-5">Macedonian News Comparison</h1>

        @if($fetchedNews)
            <form action="{{ route('store') }}" method="POST">
                @csrf
                <input type="hidden" name="articles" value="{{ json_encode($fetchedNews) }}">
                <button type="submit" class="btn btn-info mt-3">Store Fetched Articles</button>
            </form>
            <div class="row">
                @foreach($news as $result)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">News 1: {{ $result['news1_title'] }}</h5>
                                <p class="card-text">{{ Str::limit($result['news1_content'], 200) }} <a href="{{ $result['news1_url'] }}" class="btn btn-primary">Read more</a></p>

                                <h5 class="card-title">News 2: {{ $result['news2_title'] }}</h5>
                                <p class="card-text">{{ Str::limit($result['news2_content'], 200) }} <a href="{{ $result['news2_url'] }}" class="btn btn-primary">Read more</a></p>

                                <div class="d-flex justify-content-between">
                                    <p class="badge badge-primary">Similarity (Original): {{ $result['similarity_original'] }}%</p>
                                    <p class="badge badge-secondary">Similarity (Translated): {{ $result['similarity_translated'] }}%</p>
                                </div>
                                <a href="#" class="btn btn-secondary mt-2">Compare in Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No news to display.</p>
        @endif
    </div>
@endsection
