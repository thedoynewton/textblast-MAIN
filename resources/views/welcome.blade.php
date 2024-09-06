<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
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

    @if (Auth::check())
    <script type="module">
        window.userRole = "{{ Auth::user()->role }}";
        window.adminDashboardUrl = "{{ route('admin.dashboard') }}";
        window.subadminDashboardUrl = "{{ route('subadmin.dashboard') }}";

        import '/resources/js/redirect.js';
    </script>
    @endif

</head>

<body class="bg-gray-100 h-screen relative">
    <div class="h-full flex flex-col justify-center">
        <!-- Left Side Content -->
        <div class="absolute inset-0 flex justify-start items-center">
            <div class="flex flex-col items-center ml-40 mb-16">
                <h1 class="font-semibold text-2xl text-center text-primary">USeP TEXT BROADCASTING SYSTEM</h1>
                <!-- Using the SVG file as an image source -->
                <img src="{{ asset('svg/loginIllus.svg') }}" alt="Broadcasting Image" class="w-[400px] h-auto lg:w-[500px] lg:h-auto">
                <p class="text-gray-500 text-sm">
                    Copyright © 2024. All Rights Reserved.
                </p>
                <div class="">
                    <a href="#" class="text-primary hover:underline mx-2">Terms of Use</a> |
                    <a href="#" class="text-primary hover:underline mx-2">Privacy Policy</a>
                </div>
            </div>
        </div>

        <!-- Right Side Login Form -->
        <div class="floating-panel w-full max-w-md p-8 text-center bg-white">
            @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
            @endif
            @guest
            <img src="/images/SePhi Favicon.png" alt="USeP Logo" class="w-24 h-24 mx-auto">
            <h1 class="font-bold text-2xl text-center mt-4 text-primary">WELCOME BACK</h1>
            <p class="text-center text-gray-600 mt-2 mb-12">Proceed to login by selecting login options</p>

            <!-- Google Login Button -->
            <a href="{{ url('auth/google') }}" class="inline-flex items-center justify-center px-4 py-2 mb-4 bg-primary text-white font-semibold rounded-lg shadow-md w-full">
                Continue with Google
            </a>

            <!-- Divider -->
            <div class="flex items-center my-4">
                <hr class="flex-grow border-gray-300">
                <span class="mx-4 text-gray-500">or</span>
                <hr class="flex-grow border-gray-300">
            </div>

            <!-- Email Login Form -->
            <form action="{{ route('login.email') }}" method="POST">
                @csrf

                <!-- Display Validation Errors -->
                @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4 text-left">
                    @foreach ($errors->all() as $error)
                    @if ($error == 'The selected email is invalid.')
                    <div>This USeP email does not have access to this System. For concerns please contact sdmd@usep.edu.ph.</div>
                    @else
                    <div>{{ $error }}</div>
                    @endif
                    @endforeach
                </div>
                @endif

                <!-- Email Input -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-left mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your USeP email" class="border rounded border-gray-500 py-2 px-3 w-full" required>
                </div>

                <!-- Email Login Button -->
                <button type="submit" class="bg-primary text-white font-semibold px-4 py-2 rounded-lg w-full">
                    Continue with email
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-14">
                <div class="text-center">
                    <p class="text-gray-500 mb-2">Having Trouble?</p>
                    <a href="#" class="text-primary">Send us a message</a>
                </div>
            </div>
            @endguest
        </div>

        <!-- Wave SVG -->
        <img src="/images/wave.png" alt="Wave Effect" class="absolute bottom-0 left-0 w-full h-auto z-0">
    </div>
</body>

</html>