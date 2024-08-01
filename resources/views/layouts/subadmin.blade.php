<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SubAdmin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        :root {
            --primary-bg: #CCA841;
            --primary-text: #000000;
            --secondary-bg: #FFFFFF;
            --secondary-text: #000000;
        }

        [data-theme="dark"] {
            --primary-bg: #242424;
            --primary-text: #000000;
            --secondary-bg: #777777;
            --secondary-text: #000000;
        }

        [data-theme="light"] {
            --primary-bg: #CCA841;
            --primary-text: #000000;
            --secondary-bg: #FFFFFF;
            --secondary-text: #000000;
        }

        [data-theme="rosyred"] {
            --primary-bg: #66070e;
            --primary-text: #000000;
            --secondary-bg: #ffcdcd94;
            --secondary-text: #000000;
        }

        [data-theme="theme2"] {
            --primary-bg: #ffb7b2;
            --primary-text: #4a4a4a;
            --secondary-bg: #c7ceea;
            --secondary-text: #4a4a4a;
        }

        [data-theme="theme3"] {
            --primary-bg: #b2f7ef;
            --primary-text: #4a4a4a;
            --secondary-bg: #b2b7f7;
            --secondary-text: #4a4a4a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--secondary-bg);
            color: var(--secondary-text);
        }

        .bg-primary {
            background-color: var(--primary-bg);
        }

        .text-primary {
            color: var(--primary-text);
        }

        .w-73 {
            width: 18.25rem; /* 73 x 0.25rem */
        }
        .ml-73 {
            margin-left: 18.25rem; /* 73 x 0.25rem */
        }
        .text-gradient {
            background: linear-gradient(90deg, #614514, #C78E29);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .z-50 {
            z-index: 50;
        }

        .dropdown-item {
            width: 100%;
            padding: 0.5rem 1rem;
            text-align: left;
            display: block;
        }

        .dropdown-item:hover {
            background-color: #f3f3f3;
        }
    </style>
    <script>
        function toggleDropdown() {
            document.getElementById("dropdown").classList.toggle("hidden");
        }

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            toggleDropdown(); // Hide the dropdown after selection
        }

        function loadTheme() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        }

        window.onload = loadTheme;
    </script>
</head>
<body class="h-screen flex">
    <!-- Sidebar -->
    <div class="bg-white w-73 shadow-lg flex flex-col justify-between fixed h-full">
        <!-- Menu -->
        <div>
            <div class="flex items-center my-5 align-center pl-4">
                <img src="/images/SePhi Final Icon 1.png" class="w-16 h-auto"/>
                <h1 class="text-gradient font-semibold text-xl">Text Broadcasting</h1>
            </div>

            <hr class="my-4 border-t-2 border-gray-200 w-full">

            <div class="flex items-center justify-center w-full my-10">
                <img src="{{ Auth::user()->avatar }}" alt="user profile" class="w-10 h-auto rounded-full">
                <p class="text-black pl-2 text-sm font-medium">{{ strtok(Auth::user()->email, '@') }}</p>
                <div class="ml-2 px-2 py-1 h-6 text-black font-semibold text-xs rounded" style="background-color: rgba(204, 168, 65, 0.4);">
                    edu
                </div>
                <div class="relative">
                    <img src="/images/SettingsIcon.png" class="ml-2 w-5 h-5 cursor-pointer" onclick="toggleDropdown()">
                    <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg hidden">
                        <a href="{{ url('logout') }}" class="dropdown-item text-gray-800 hover:bg-gray-200">Logout</a>
                        <hr class="border-t-2 border-gray-200 my-2">
                        <button onclick="setTheme('light')" class="dropdown-item text-gray-800 hover:bg-gray-200">Light Theme</button>
                        <button onclick="setTheme('dark')" class="dropdown-item text-gray-800 hover:bg-gray-200">Dark Theme</button>
                        <button onclick="setTheme('rosyred')" class="dropdown-item text-gray-800 hover:bg-gray-200">Rosy Red</button>
                        <button onclick="setTheme('theme2')" class="dropdown-item text-gray-800 hover:bg-gray-200">Theme 2</button>
                        <button onclick="setTheme('theme3')" class="dropdown-item text-gray-800 hover:bg-gray-200">Theme 3</button>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-t-2 border-gray-200 w-full">

            <ul class="mt-5">
                <li class="hover:bg-gray-200 my-3">
                    <a href="{{ route('subadmin.dashboard') }}" class="px-10 py-3 flex items-center w-full h-full font-semibold text-lg">
                        <img src="/images/dashboard.png" class="w-4 h-4 mr-2">
                        Dashboard
                    </a>
                </li>
                <li class="hover:bg-gray-200 my-3">
                    <a href="{{ route('subadmin.messages') }}" class="px-10 py-3 flex items-center w-full h-full font-semibold text-lg">
                        <img src="/images/message.png" class="w-4 h-4 mr-2">
                        Messages
                    </a>
                </li>
                <li class="hover:bg-gray-200 my-3">
                    <a href="{{ route('subadmin.analytics') }}" class="px-10 py-3 flex items-center w-full h-full font-semibold text-lg">
                        <img src="/images/analytics.png" class="w-4 h-4 mr-2">
                        Analytics
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="bg-gray-200 flex-reverse relative flex-1 ml-73">
        <div class="absolute w-full h-36 shadow-md bg-primary"></div>
        <div class="relative flex-1 p-8">
            <!-- Page Content -->
            <h1 class="text-2xl font-semibold mb-4 text-white">@yield('title')</h1>
            <div class="mt-10">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
