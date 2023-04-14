<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
    {{-- Left navbar links --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    {{-- SEARCH FORM --}}
    <form class="form-inline ml-3">
        @include('components.menu.nav-item-filter')
    </form>

    {{-- Right navbar links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Messages Dropdown Menu --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">0</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>

        {{-- Notifications Dropdown Menu --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">0</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">0 Notifications</span>
            </div>
        </li>

        {{-- Users --}}
        @include('partials._legacy-users-menu')
    </ul>
</nav>
