@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-5">Compare Articles or Texts</h1>

        <!-- Compare Saved Articles -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Compare Saved Articles</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('compare.saved.articles') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="article1">Select First Article:</label>
                        <select class="form-control" id="article1" name="article1_id" required>
                            <option value="" disabled selected>Select an article</option>
                            @foreach($articles as $article)
                                <option value="{{ $article->id }}">{{ $article->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="article2">Select Second Article:</label>
                        <select class="form-control" id="article2" name="article2_id" required>
                            <option value="" disabled selected>Select an article</option>
                            @foreach($articles as $article)
                                <option value="{{ $article->id }}">{{ $article->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Compare Articles</button>
                </form>
            </div>
        </div>

        <!-- Compare Custom Texts -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Compare Custom Texts</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('compare.texts') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="text1">Enter First Text:</label>
                        <textarea class="form-control" id="text1" name="text1" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="text2">Enter Second Text:</label>
                        <textarea class="form-control" id="text2" name="text2" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Compare Texts</button>
                </form>
            </div>
        </div>
    </div>
@endsection
