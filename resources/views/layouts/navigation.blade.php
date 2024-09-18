<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Home Link -->
        <a class="navbar-brand" href="{{ route('index') }}">
            {{ __('Home') }}
        </a>

        <!-- Hamburger (Mobile Toggle) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Left side (Links) -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('compare.form') ? 'active' : '' }}" href="{{ route('compare.form') }}">
                            {{ __('Compare Articles') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('fetchNews') ? 'active' : '' }}" href="{{ route('fetchNews') }}">
                            {{ __('Fetch News') }}
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- Right side (Authentication/Settings) -->
            <ul class="navbar-nav ms-auto">
                @guest
                    <!-- Login and Register links for guests -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @endguest

                @auth
                    <!-- Dropdown for Authenticated User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.likes') }}">{{ __('My Likes') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.bookmarks') }}">{{ __('My Bookmarks') }}</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
