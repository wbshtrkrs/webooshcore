<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{!! route('admin.dashboard') !!}">
                <i class="menu-icon mdi mdi-television"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#pages-dropdown" aria-expanded="false" aria-controls="pages-dropdown">
                <i class="menu-icon mdi mdi-trackpad"></i>
                <span class="menu-title">Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="pages-dropdown">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{!! route('admin.pages') !!}">Information Page</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>