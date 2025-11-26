<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text font-weight-light">Stok MTC</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Item -->
                <li class="nav-item">
                    <a href="{{ url('item') }}" class="nav-link {{ request()->is('item*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Item</p>
                    </a>
                </li>

                <!-- Stok -->
                <li
                    class="nav-item has-treeview {{ request()->is('in*') || request()->is('out*') || request()->is('laporan*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('in*') || request()->is('out*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Stok <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('in') }}" class="nav-link {{ request()->is('in*') ? 'active' : '' }}">
                                <i class="fas fa-arrow-down nav-icon"></i>
                                <p>In Trading</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('out') }}" class="nav-link {{ request()->is('out*') ? 'active' : '' }}">
                                <i class="fas fa-arrow-up nav-icon"></i>
                                <p>Out Used</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan') }}"
                                class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                                <i class="fas fa-exchange-alt nav-icon"></i>
                                <p>Stok Flow</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Data -->
                <li
                    class="nav-item has-treeview {{ request()->is('kategori*') || request()->is('uom*') || request()->is('supplier*') || request()->is('divisi*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('kategori*') || request()->is('uom*') || request()->is('supplier*') || request()->is('divisi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Data <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('kategori') }}"
                                class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}">
                                <i class="fas fa-tags nav-icon"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('uom') }}"
                                class="nav-link {{ request()->is('uom*') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>UoM</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('supplier') }}"
                                class="nav-link {{ request()->is('supplier*') ? 'active' : '' }}">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>Supplier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('divisi') }}"
                                class="nav-link {{ request()->is('divisi*') ? 'active' : '' }}">
                                <i class="fas fa-building nav-icon"></i>
                                <p>Divisi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User -->
                @if (in_array(auth()->user()->role, ['master']))
                    <li class="nav-item">
                        <a href="{{ url('user') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>User</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
