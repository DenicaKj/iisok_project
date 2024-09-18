@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="h4 text-primary">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <p class="h5 text-center text-muted">
                        {{ __("You're logged in!") }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
