<nav x-data="{ open: false, showLogoutModal: false }"
    class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 w-full">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-3 group flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div
                        class="bg-gradient-to-br from-white to-white-700 rounded-full p-2 group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn"
                            class="h-8 w-8 object-contain">
                    </div>
                    <div class="hidden xl:block">
                        <h2 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">MagangIn
                        </h2>
                        <p class="text-xs text-gray-500">BEM KM FTI UNAND</p>
                    </div>
                </a>
            </div>

            <!-- Main Navigation Links -->
            <div class="hidden md:flex items-center justify-center flex-1 px-4">
                <div class="flex items-center space-x-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg hover:bg-blue-50 whitespace-nowrap
                               {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:text-blue-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>

                    <!-- Navigation Menu Items -->
                    @php
                    $mainNavigation = [
                    ['url' => '/info-or', 'label' => 'Info OR', 'icon' => 'fas fa-info-circle', 'pattern' => 'info-or'],
                    ['url' => '/pendaftar', 'label' => 'Data Pendaftar', 'icon' => 'fas fa-users', 'pattern' =>
                    'pendaftar'],
                    ['url' => '/jadwal-kegiatan', 'label' => 'Kelola Kegiatan', 'icon' => 'fas fa-tasks', 'pattern' =>
                    'jadwal-kegiatan'],
                    ['url' => '/penilaian', 'label' => 'Penilaian Magang', 'icon' => 'fas fa-star', 'pattern' =>
                    'penilaian'],
                    ['url' => '/users', 'label' => 'Kelola User', 'icon' => 'fas fa-user-cog', 'pattern' => 'users*']
                    ];

                    // Check if any wawancara submenu is active
                    $wawancaraActive = request()->is('jadwal-seleksi*') || request()->is('penilaian-wawancara*') ||
                    request()->is('hasilwawancara*');
                    @endphp

                    @foreach($mainNavigation as $item)
                    @php
                    $isActive = Request::is($item['pattern']);
                    $href = url($item['url']);
                    @endphp

                    <a href="{{ $href }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg hover:bg-blue-50 whitespace-nowrap
                                  {{ $isActive ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:text-blue-700' }}"
                        aria-current="{{ $isActive ? 'page' : 'false' }}">
                        <i class="{{ $item['icon'] }} mr-2 text-sm"></i>
                        {{ $item['label'] }}
                    </a>
                    @endforeach

                    <!-- Data Wawancara Dropdown -->
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button @click="open = !open"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg hover:bg-blue-50 whitespace-nowrap
                                   {{ $wawancaraActive ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:text-blue-700' }}"
                            :class="{ 'ring-2 ring-blue-300': open }">
                            <i class="fas fa-clipboard-list mr-2 text-sm"></i>
                            Data Wawancara
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
                            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
                            class="absolute right-0 z-50 mt-2 w-72 rounded-xl bg-white shadow-xl ring-1 ring-black/5 border border-gray-100"
                            style="transform: translateX(25%);">
                            <div class="py-2">
                                <a href="{{ route('jadwal-seleksi.index') }}"
                                    class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 hover:bg-blue-50 hover:text-blue-700
                                          {{ request()->is('jadwal-seleksi*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-alt text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Jadwal Wawancara</div>
                                        <div class="text-xs text-gray-500">Atur waktu interview</div>
                                    </div>
                                </a>
                                <a href="{{ route('penilaian-wawancara.index') }}"
                                    class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 hover:bg-blue-50 hover:text-blue-700
                                          {{ request()->is('penilaian-wawancara*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-clipboard-check text-yellow-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Penilaian Wawancara</div>
                                        <div class="text-xs text-gray-500">Form penilaian</div>
                                    </div>
                                </a>
                                <a href="{{ route('hasilwawancara.index') }}"
                                    class="flex items-center px-4 py-3 text-sm font-medium transition-all duration-200 hover:bg-blue-50 hover:text-blue-700
                                          {{ request()->is('hasilwawancara*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-chart-bar text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">Hasil Wawancara</div>
                                        <div class="text-xs text-gray-500">Lihat hasil interview</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <!-- Settings Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-500 bg-white hover:text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                        <div class="flex items-center space-x-3">
                            <!-- User Avatar -->
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-semibold uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="font-medium text-gray-900 text-sm">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-32">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 transition-transform duration-200"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="open" x-cloak @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5">

                        <!-- User Info Header -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold uppercase">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                    @if(Auth::user()->role)
                                    <div class="text-xs text-blue-600 font-medium capitalize">{{ Auth::user()->role }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-sm hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                Profile Settings
                            </a>

                            <a href="#" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50 transition-colors">
                                <i class="fas fa-cog mr-3 text-gray-400"></i>
                                Account Settings
                            </a>

                            <a href="#" class="flex items-center px-4 py-2 text-sm hover:bg-gray-50 transition-colors">
                                <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                                Help & Support
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Logout Button -->
                            <button @click.stop="$refs.logoutModal.showModal()"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <!-- Mobile Dashboard Link -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                Dashboard
            </a>

            <!-- Mobile Navigation Items -->
            @php
            $mobileNavigation = [
            ['url' => '/info-or', 'label' => 'Info OR', 'icon' => 'fas fa-info-circle', 'pattern' => 'info-or'],
            ['url' => '/pendaftar', 'label' => 'Data Pendaftar', 'icon' => 'fas fa-users', 'pattern' => 'pendaftar'],
            ['url' => '/jadwal-kegiatan', 'label' => 'Kelola Kegiatan', 'icon' => 'fas fa-tasks', 'pattern' =>
            'jadwal-kegiatan'],
            ['url' => '/penilaian', 'label' => 'Penilaian Magang', 'icon' => 'fas fa-star', 'pattern' => 'penilaian'],
            ['url' => '/users', 'label' => 'Kelola User', 'icon' => 'fas fa-user-cog', 'pattern' => 'users*']
            ];
            $wawancaraActive = request()->is('jadwal-seleksi*') || request()->is('penilaian-wawancara*') ||
            request()->is('hasilwawancara*');
            @endphp

            @foreach($mobileNavigation as $item)
            @php
            $isActive = Request::is($item['pattern']);
            $href = url($item['url']);
            @endphp

            <a href="{{ $href }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ $isActive 
                             ? 'bg-blue-100 text-blue-700' 
                             : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="{{ $item['icon'] }} mr-3 text-sm"></i>
                {{ $item['label'] }}
            </a>
            @endforeach

            <!-- Mobile Wawancara Menu -->
            <div x-data="{ openMobile: {{ $wawancaraActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="openMobile = !openMobile"
                    class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm font-medium transition-colors
                               {{ $wawancaraActive ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-sm"></i>
                        Data Wawancara
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                        :class="{ 'rotate-180': openMobile }"></i>
                </button>

                <div x-show="openMobile" x-collapse class="ml-6 space-y-1 border-l-2 border-gray-200 pl-4">
                    <a href="{{ route('jadwal-seleksi.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors
                              {{ request()->is('jadwal-seleksi*') ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-alt mr-3 text-green-600 text-sm"></i>
                        Jadwal Wawancara
                    </a>
                    <a href="{{ route('penilaian-wawancara.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors
                              {{ request()->is('penilaian-wawancara*') ? 'bg-yellow-50 text-yellow-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-clipboard-check mr-3 text-yellow-600 text-sm"></i>
                        Penilaian Wawancara
                    </a>
                    <a href="{{ route('hasilwawancara.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-colors
                              {{ request()->is('hasilwawancara*') ? 'bg-purple-50 text-purple-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-bar mr-3 text-purple-600 text-sm"></i>
                        Hasil Wawancara
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-3 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 px-2 space-y-1">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                    Profile
                </a>

                <button @click.stop="$refs.logoutModal.showModal()"
                    class="flex items-center w-full px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                    <i class="fas fa-sign-out-alt mr-3 text-red-500"></i>
                    Log Out
                </button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <dialog x-ref="logoutModal" class="bg-transparent backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto overflow-hidden transform" @click.stop>

                <!-- Modal Header -->
                <div class="flex justify-between items-center p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Logout</h3>
                    <button @click="$refs.logoutModal.close()" type="button"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin keluar dari sistem?</p>

                    <div class="flex justify-center space-x-4">
                        <button @click="$refs.logoutModal.close()" type="button"
                            class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Ya, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </dialog>
</nav>