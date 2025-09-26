<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0">

    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-4 sidebar-header pb-8">

        <div class="logo" :class="sidebarToggle ? 'hidden' : ''">
            <div class="flex gap-x-1">
                <img src="{{ asset('images/logo/jayaraya.png') }}" alt="Logo" class="w-10" />
                <img src="{{ asset('images/logo/pemadam.png') }}" alt="Logo" class="w-12 me-3" />
                <p class="text-2xl pt-1 font-medium text-gray-800 dark:text-gray-200">
                    <span class="font-thin">e-</span>RISPK
                </p>
            </div>
        </div>

        <img class="logo-icon w-10" :class="sidebarToggle ? 'lg:block' : 'hidden'"
            src="{{ asset('images/logo/jayaraya.png') }}" alt="Logo" />
    </div>
    <div class="hidden lg:block w-full border-t border-gray-200 dark:border-gray-800 absolute left-0 top-[76px]"></div>
    <!-- END SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav x-data="{ selected: $persist('Dashboard') }">
            <div>
                <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">MENU</span>
                </h3>

                <ul class="flex flex-col gap-4 mb-6">
                    <!-- Menu Item Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="menu-item group {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <x-fas-desktop
                                class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}" />
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Dashboard</span>
                        </a>
                    </li>

                    <!-- Menu Item Master -->
                    <li>
                        <a href="#" @click.prevent="selected = (selected === 'Master' ? '' : 'Master')"
                            class="menu-item group"
                            :class="(selected === 'Master') ? 'menu-item-active' : 'menu-item-inactive'">
                            <x-fas-database class="w-5 h-5"
                                x-bind:class="(selected === 'Master') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" />
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">Master</span>
                            <svg class="menu-item-arrow"
                                :class="[(selected === 'Master') ? 'menu-item-arrow-active' :
                                    'menu-item-arrow-inactive',
                                    sidebarToggle ? 'lg:hidden' : ''
                                ]"
                                width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <!-- Dropdown Master -->
                        <div class="overflow-hidden transform translate"
                            :class="(selected === 'Master') ? 'block' : 'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                @can('category index')
                                <li>
                                    <a href="{{ route('categories.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'categories' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Kategori
                                    </a>
                                </li>
                                @endcan

                                @can('status index')
                                <li>
                                    <a href="{{ route('statuses.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'statuses' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Status
                                    </a>
                                </li>
                                @endcan

                                @can('metode index')
                                <li>
                                    <a href="{{ route('metode.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'metode' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Metode Cara Ukur
                                    </a>
                                </li>
                                @endcan

                                @can('tipe index')
                                <li>
                                    <a href="{{ route('tipe.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'tipe' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Tipe Rencana Aksi
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    <!-- END Menu Item Master -->

                    <!-- Menu Item UserManagement -->
                    <li>
                        <a href="#"
                            @click.prevent="selected = (selected === 'UserManagement' ? '' : 'UserManagement')"
                            class="menu-item group"
                            :class="(selected === 'UserManagement') ? 'menu-item-active' : 'menu-item-inactive'">
                            <x-fas-user-cog class="w-5 h-5"
                                x-bind:class="(selected === 'UserManagement') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" />
                            <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">User
                                Management</span>
                            <svg class="menu-item-arrow"
                                :class="[(selected === 'UserManagement') ? 'menu-item-arrow-active' :
                                    'menu-item-arrow-inactive',
                                    sidebarToggle ? 'lg:hidden' : ''
                                ]"
                                width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>

                        <!-- Dropdown UserManagement -->
                        <div class="overflow-hidden transform translate"
                            :class="(selected === 'UserManagement') ? 'block' : 'hidden'">
                            <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9">
                                <li>
                                    <a href="{{ route('permissions.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'permissions' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Permission
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('roles.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'roles' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        Role
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('users.index') }}" class="menu-dropdown-item group"
                                        :class="page === 'users' ? 'menu-dropdown-item-active' :
                                            'menu-dropdown-item-inactive'">
                                        User
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- END Menu Item UserManagement -->
                </ul>
            </div>
        </nav>
    </div>
</aside>
