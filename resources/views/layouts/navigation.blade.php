<nav x-data="{ open: false, showLogoutModal: false }"
    class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40">
    <div class="w-full px-4 lg:px-6">
        <div class="flex items-center justify-between h-16 w-full">
            <div class="flex items-center group flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div
                        class="bg-gradient-to-br from-white to-white-700 rounded-full p-1.5 group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logomagangin.png') }}" alt="Logo MagangIn"
                            class="h-7 w-7 object-contain">
                    </div>
                    <div class="hidden lg:block">
                        <h2
                            class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors leading-tight">
                            MagangIn
                        </h2>
                        <p class="text-[10px] text-gray-500 leading-tight">BEM KM FTI UNAND</p>
                    </div>
                </a>
            </div>


            <div class="hidden md:flex items-center justify-center flex-1 px-4">
                <div class="flex items-center space-x-1">
                    @if(Auth::user()->role == 'superadmin')

                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>

                    <a href="{{ route('info-or.index') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->is('info-or*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-info-circle mr-2"></i> Info OR
                    </a>

                    <a href="{{ route('pendaftar.index') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->is('pendaftar*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-users mr-2"></i> Data Pendaftar
                    </a>

                    @php
                    $wawancaraActive = request()->is('jadwal-seleksi*') || request()->is('penilaian-wawancara*') ||
                    request()->is('hasilwawancara*');
                    @endphp
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
        {{ $wawancaraActive ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                            <i class="fas fa-clipboard-list mr-2 text-sm"></i>
                            <span>Data Wawancara</span>
                            <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false" x-transition
                            class="absolute right-0 z-50 mt-2 w-72 rounded-xl bg-white shadow-xl ring-1 ring-black/5 border border-gray-100"
                            style="transform: translateX(25%);">
                            <div class="py-2">
                                <a href="{{ route('jadwal-seleksi.index') }}"
                                    class="flex items-center px-4 py-3 text-sm transition-colors
    {{ request()->is('jadwal-seleksi*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                                    <i class="fas fa-calendar-alt text-green-600 mr-3"></i> Jadwal Wawancara
                                </a>
                                <a href="{{ route('penilaian-wawancara.index') }}"
                                    class="flex items-center px-4 py-3 text-sm transition-colors
    {{ request()->is('penilaian-wawancara*') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }}">
                                    <i class="fas fa-clipboard-check text-yellow-600 mr-3"></i> Penilaian Wawancara
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('jadwal-kegiatan.index') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->is('jadwal-kegiatan*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tasks mr-2"></i> Kelola Kegiatan
                    </a>

                    <a href="{{ route('penilaian.index') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->routeIs('penilaian.index') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-star mr-2"></i> Penilaian Magang
                    </a>

                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center justify-start whitespace-nowrap px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
    {{ request()->is('users*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-user-cog mr-2"></i> Kelola User
                    </a>


                    @elseif(Auth::user()->role == 'admin')
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>

                    <a href="{{ route('pendaftar.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('pendaftar*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-users mr-2"></i> Data Pendaftar
                    </a>

                    <a href="{{ route('jadwal-kegiatan.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('jadwal-kegiatan*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Data Kegiatan
                    </a>

                    <a href="{{ route('penilaian.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('penilaian*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-star mr-2"></i> Penilaian Magang
                    </a>

                    @elseif(Auth::user()->role == 'mahasiswa')
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>

                    <a href="{{ route('mahasiswa.jadwal-seleksi') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('seleksi-wawancara*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-calendar-check mr-2"></i> Jadwal Wawancara
                    </a>

                    <a href="{{ route('kelulusanwawancara.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('kelulusan-wawancara*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-clipboard-check mr-2"></i> Kelulusan Wawancara
                    </a>

                    <a href="{{ route('jadwal-kegiatan.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg
                {{ request()->is('jadwal-kegiatan*') ? 'text-blue-600 bg-blue-50 font-semibold shadow-sm' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-calendar-alt mr-2"></i> Data Kegiatan
                    </a>
                    <a href="{{ route('kelulusan-magang.index') }}"
                        class=" inline-flex items-center px-3 py-2 text-sm font-medium transition-all duration-200
                        rounded-lg hover:bg-blue-50
                        {{ request()->is('kelulusan-magang*') ? 'text-blue-600 bg-blue-50 font-semibold' : 'text-gray-700 hover:text-blue-700' }}">
                        <i class="fas fa-graduation-cap mr-2"></i> Kelulusan Magang
                    </a>
                    @endif
                </div>
            </div>


            <div class="flex items-center flex-shrink-0">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-2 py-1.5 border border-transparent text-sm font-medium rounded-lg text-gray-600 bg-white hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center bg-gray-100">
                                <img src="{{ asset(path: 'images/profile-icon.jpeg') }}" alt="User Avatar"
                                    class="w-full h-full object-cover object-center">
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="font-medium text-gray-900 text-sm truncate max-w-[120px]">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 truncate max-w-[120px]">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>
                            <svg class="fill-current h-4 w-4 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg border border-gray-200">

                        <div class="px-4 py-3 border-b border-gray-100 flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center bg-gray-100">
                                <img src="{{ asset(path: 'images/profile-icon.jpeg') }}" alt="User Avatar"
                                    class="w-full h-full object-cover object-center">
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ Auth::user()->email }}</div>
                                @if(Auth::user()->role)
                                <div class="text-xs text-blue-600 font-medium capitalize mt-0.5">
                                    {{ Auth::user()->role }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 rounded-md">
                                <i class="fas fa-id-card-alt mr-3 text-blue-500"></i>
                                <span>Edit Profil</span>
                            </a>

                            <a href="{{ route('profile.password.edit') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 rounded-md">
                                <i class="fas fa-lock mr-3 text-amber-500"></i>
                                <span>Ubah Password</span>
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-3 text-red-500"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>


            </div>


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

    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                    {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-tachometer-alt mr-3 text-gray-400"></i>
                Dashboard
            </a>

            @php
            $mobileNavigation = [
            ['url' => '/info-or', 'label' => 'Info OR', 'icon' => 'fas fa-info-circle', 'pattern' => 'info-or*'],
            ['url' => '/pendaftar', 'label' => 'Data Pendaftar', 'icon' => 'fas fa-users', 'pattern' =>
            'pendaftar*'],
            ['url' => '/jadwal-kegiatan', 'label' => 'Kelola Kegiatan', 'icon' => 'fas fa-tasks', 'pattern' =>
            'jadwal-kegiatan*'],
            ['url' => '/penilaian', 'label' => 'Penilaian Magang', 'icon' => 'fas fa-star', 'pattern' =>
            'penilaian*'],
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

            @if(Auth::user()->role == 'superadmin')
            <a href="{{ $href }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                                  {{ $isActive 
                                    ? 'bg-blue-100 text-blue-700' 
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="{{ $item['icon'] }} mr-3 text-sm"></i>
                {{ $item['label'] }}
            </a>
            @endif

            @if(Auth::user()->role == 'admin')
            @if(in_array($item['label'], ['Data Pendaftar', 'Data Kegiatan', 'Penilaian Magang']))
            <a href="{{ $href }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                                  {{ $isActive 
                                    ? 'bg-blue-100 text-blue-700' 
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="{{ $item['icon'] }} mr-3 text-sm"></i>
                {{ $item['label'] }}
            </a>
            @endif
            @endif

            @if(Auth::user()->role == 'mahasiswa')
            @if(in_array($item['label'], ['Data Kegiatan', 'Kelulusan Magang']))
            <a href="{{ $href }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                                  {{ $isActive 
                                    ? 'bg-blue-100 text-blue-700' 
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="{{ $item['icon'] }} mr-3 text-sm"></i>
                {{ $item['label'] }}
            </a>
            @endif
            @endif
            @endforeach

            @if(Auth::user()->role == 'superadmin')
            <div x-data="{ openMobile: {{ $wawancaraActive ? 'true' : 'false' }} }" x-cloak class="space-y-1">
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
            @endif
            @if(Auth::user()->role == 'mahasiswa')
            <a href="{{ route('mahasiswa.jadwal-seleksi') }}"
                class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->is('seleksi-wawancara*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-calendar-check mr-3 text-gray-400"></i> Jadwal Wawancara
            </a>
            <a href="{{ route('kelulusanwawancara.index') }}"
                class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->is('kelulusan-wawancara*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-clipboard-check mr-3 text-gray-400"></i> Kelulusan Wawancara
            </a>
            <a href="{{ route('kelulusan-magang.index') }}"
                class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition-colors
                        {{ request()->is('kelulusan-magang*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-graduation-cap mr-3 text-gray-400"></i> Kelulusan Magang
            </a>
            @endif
            </div>

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

    <dialog x-ref="logoutModal" class="bg-transparent backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto overflow-hidden transform" @click.stop>

                <div class="flex justify-between items-center p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Logout</h3>
                    <button @click="$refs.logoutModal.close()" type="button"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

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