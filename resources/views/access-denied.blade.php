<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Standard favicon -->
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="64x64">
    <link rel="icon" href="/images/SePhi Favicon.png" type="image/png" sizes="128x128">

    <style>
        :root {
            --primary-color: #800000;
        }

        .bg-primary {
            background-color: var(--primary-color);
        }

        .text-primary {
            color: var(--primary-color);
        }

        .border-primary {
            border-color: var(--primary-color);
        }

        .floating-panel {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transform: translateY(-10px);
            z-index: 10;
            position: relative;
            margin-left: auto;
            margin-right: 5%;
        }
    </style>
</head>

<body class="bg-gray-100 h-screen relative">
    <div class="h-full flex flex-col justify-center">
        <!-- Left Side Content -->
        <div class="absolute inset-0 flex justify-start items-center">
            <div class="flex flex-col items-center ml-40 mb-16">
                <h1 class="font-semibold text-2xl text-center text-primary">USeP TEXT BROADCASTING SYSTEM</h1>
                <!-- Using the SVG file as an image source -->
                <img src="{{ asset('svg/loginIllus.svg') }}" alt="Access Denied Image" class="w-[400px] h-auto lg:w-[500px] lg:h-auto">
                <p class="text-gray-500 text-sm">
                    Copyright © 2024. All Rights Reserved.
                </p>
                <div class="">
                    <a href="#" class="text-primary hover:underline mx-2">Terms of Use</a> |
                    <a href="#" class="text-primary hover:underline mx-2">Privacy Policy</a>
                </div>
            </div>
        </div>

        <!-- Right Side Content -->
        <div class="floating-panel w-full max-w-md p-8 text-center bg-white">
            <h1 class="text-4xl font-bold text-red-600">Access Denied</h1>
            <p class="text-lg text-gray-700 mt-4">You do not have permission to access this page.</p>

            @if (Auth::check())
                @if (Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-75">
                        Back to Dashboard
                    </a>
                @elseif (Auth::user()->role === 'subadmin')
                    <a href="{{ route('subadmin.dashboard') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-75">
                        Back to Dashboard
                    </a>
                @endif
            @endif

            <a href="{{ route('logout') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-opacity-75">
                Logout
            </a>
        </div>

        <!-- Wave SVG -->
        <img src="/images/wave.png" alt="Wave Effect" class="absolute bottom-0 left-0 w-full h-auto z-0">
    </div>
</body>

</html>
