<aside class="main-sidebar sidebar-dark-primary elevation-4">>


    <!-- Sidebar -->
    <div class="sidebar">

        <div class="row user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">

            </div>
            <div class="info">
                <a href="#" class="d-block">Stok MTC</a>
            </div>
        </div>


        <!-- Sidebar Menu -->
        <nav class="mt-2 d-flex flex-column">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/*') ? 'active' : '' }}">
                        <i class="nav-icon fa  fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('item') }}" class="nav-link {{ request()->is('item*') ? 'active' : '' }}">
                        <i class="nav-icon fa  fa-box"></i>
                        <p>Item</p>
                    </a>
                </li>

                <li
                    class="nav-item has-treeview  {{ request()->is('in*') || request()->is('out*') || request()->is('laporan*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->is('in*') || request()->is('out*') ? 'active' : '' }}">
                        <i class="nav-icon fas fas fa-clipboard-list"></i>
                        <p>Stok <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{ url('in') }}" class="nav-link {{ request()->is('in*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-arrow-down"></i>
                                <p>In Trading</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('out') }}" class="nav-link {{ request()->is('out*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-arrow-up"></i>
                                <p>Out Used</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('laporan') }}"
                                class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Stok Flow</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li
                    class="nav-item has-treeview 
    {{ request()->is('kategori*') || request()->is('uom*') || request()->is('supplier*') || request()->is('divisi*') ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link 
        {{ request()->is('kategori*') || request()->is('uom*') || request()->is('supplier*') || request()->is('divisi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>
                            Data
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('kategori') }}"
                                class="nav-link {{ request()->is('kategori*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Kategori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('uom') }}"
                                class="nav-link {{ request()->is('uom*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>UoM</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('supplier') }}"
                                class="nav-link {{ request()->is('supplier*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>Suplier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('divisi') }}"
                                class="nav-link {{ request()->is('divisi*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Divisi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @if (in_array(auth()->user()->role, ['master']))
                    <li class="nav-item">
                        <a href="{{ url('user') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                            <i class="nav-icon fa  fa-user"></i>
                            <p>User</p>
                        </a>
                    </li>
                @endif

                <div class="user-panel mt-3 pb-3 mb-3 d-flex">

                </div>

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-white text-left">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>


            </ul>
        </nav>

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
