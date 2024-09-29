<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sub-Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Standard favicon -->
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="64x64">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="128x128">

    <!-- Link to Vite CSS and JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="h-screen flex">
    <!-- Sidebar -->
    <div class="bg-white w-73 shadow-lg flex flex-col justify-between fixed h-full">
        <!-- Menu -->
        <div>
            <div class="flex flex-col items-center mt-6">
                <img src="/images/SePhi Favicon.png" class="w-16 h-auto" />
                <h1 style="font-size: 20px;" class="mt-2 text-2xl font-bold text-red-700">SePhi</h1>
                <p style="font-size: 8px;" class="text-gray-600">Text Broadcast</p>
            </div>

            <hr class="my-4 border-t-2 border-gray-200 w-full">

            <div class="flex items-center justify-center w-full my-6">
                <img src="{{ Auth::user()->avatar }}" alt="user profile" class="w-10 h-auto rounded-full">
                <p class="text-black pl-2 text-sm font-medium">{{ strtok(Auth::user()->email, '@') }}</p>
                <div class="relative">
                    <img src="/images/SettingsIcon.png" class="ml-12 w-5 h-5 cursor-pointer" onclick="toggleDropdown()">
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg hidden">
                        <button onclick="setTheme('light')" class="dropdown-item text-gray-800 hover:bg-gray-200">Light
                            Theme</button>
                        <button onclick="setTheme('dark')" class="dropdown-item text-gray-800 hover:bg-gray-200">Dark
                            Theme</button>
                        <hr class="border-t-2 border-gray-200 my-2">
                        <a href="{{ url('logout') }}" class="dropdown-item text-gray-800 hover:bg-gray-200">Logout</a>
                    </div>
                </div>
            </div>

            <hr class="my-4 border-t-2 border-gray-200 w-full">

            <ul class="mt-5">
                <li
                    class="{{ request()->routeIs('subadmin.dashboard') ? 'button-selected font-bold' : 'button-default button-hover font-normal' }} my-3">
                    <a href="{{ route('subadmin.dashboard') }}"
                        class="px-10 py-3 flex items-center w-full h-full text-lg">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-[1.2rem]">
                            <path d="M10 3H3V10H10V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M21 3H14V10H21V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M21 14H14V21H21V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M10 14H3V21H10V14Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li
                    class="{{ request()->routeIs('subadmin.messages') ? 'button-selected font-bold' : 'button-default button-hover font-normal' }} my-3">
                    <a href="{{ route('subadmin.messages') }}"
                        class="px-10 py-3 flex items-center w-full h-full text-lg">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-[1.2rem]">
                            <path d="M22 12H16L14 15H10L8 12H2" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M5.45 5.11L2 12V18C2 18.5304 2.21071 19.0391 2.58579 19.4142C2.96086 19.7893 3.46957 20 4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V12L18.55 5.11C18.3844 4.77679 18.1292 4.49637 17.813 4.30028C17.4967 4.10419 17.1321 4.0002 16.76 4H7.24C6.86792 4.0002 6.50326 4.10419 6.18704 4.30028C5.87083 4.49637 5.61558 4.77679 5.45 5.11Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Messages
                    </a>
                </li>
                <li
                    class="{{ request()->routeIs('subadmin.analytics') ? 'button-selected font-bold' : 'button-default button-hover font-normal' }} my-3">
                    <a href="{{ route('subadmin.analytics') }}"
                        class="px-10 py-3 flex items-center w-full h-full text-lg">
                        <svg width="23" height="23" viewBox="0 0 23 23" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-[1.2rem]">
                            <g clip-path="url(#clip0_1460_227)">
                                <path
                                    d="M20.3265 15.2279C19.7168 16.6697 18.7632 17.9402 17.5491 18.9283C16.335 19.9165 14.8973 20.5921 13.3617 20.8963C11.8262 21.2004 10.2395 21.1237 8.74036 20.673C7.24127 20.2222 5.87541 19.4111 4.7622 18.3106C3.649 17.21 2.82234 15.8535 2.3545 14.3596C1.88666 12.8658 1.79189 11.2801 2.07846 9.74112C2.36504 8.20218 3.02424 6.75687 3.99843 5.53154C4.97263 4.30622 6.23215 3.33819 7.66689 2.7121"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M21.0833 11.5C21.0833 10.2415 20.8355 8.99534 20.3538 7.83264C19.8722 6.66993 19.1663 5.61348 18.2764 4.72358C17.3865 3.83369 16.3301 3.12778 15.1674 2.64617C14.0047 2.16457 12.7585 1.91669 11.5 1.91669V11.5H21.0833Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </g>
                            <defs>
                                <clipPath id="clip0_1460_227">
                                    <rect width="23" height="23" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        Analytics
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-200 relative flex-1 ml-73 overflow-auto z-0">
        <!-- Page Content -->
        <div class="relative z-10 p-8">
            <h1 class="text-2xl font-semibold mb-4 text-white">@yield('title')</h1>
            <div class="">
                @yield('content')
            </div>
        </div>
        <!-- Wave Effect -->
        <img id="waveEffect" src="/images/wave.png" alt="Wave Effect"
            class="absolute bottom-0 left-0 w-full h-auto z-0">
    </div>
</body>

</html>
