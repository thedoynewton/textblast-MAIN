<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-opacity-50 {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .bg-cover {
            background-size: cover;
        }
        .bg-white-transparent {
            background-color: rgba(255, 255, 255, 0.8); /* Adjust the transparency here */
        }
        .eagle-image {
            background-image: url('/images/eagle.jpg');
            background-size: cover;
            background-position: right;
            opacity: 0.5; /* Adjust the transparency here */
        }
    </style>
    @if (Auth::check())
        <script>
            window.onload = function() {
                @if (Auth::user()->role === 'admin')
                    window.location.href = "{{ route('admin.dashboard') }}";
                @elseif (Auth::user()->role === 'subadmin')
                    window.location.href = "{{ route('subadmin.dashboard') }}";
                @endif
            };
        </script>
    @endif
</head>
<body class="bg-gray-900 h-screen">
    <div class="flex flex-col md:flex-row h-full">
        <div class="flex items-center justify-center p-10 eagle-image relative w-full md:w-1/2">
        </div>
        <div class="flex flex-col items-center justify-center p-10 bg-gray-200 w-full md:w-1/2">
            <div class="bg-white-transparent shadow-xl rounded-lg p-8 w-full max-w-md relative z-10">
                @if (session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @guest
                    <form>
                        <img src="/images/SePhi Final Icon 1.png" class="w-1/2 h-auto mx-auto"/>
                        <h1 class="font-bold text-4xl text-center">Welcome to USeP</h1>
                        <h1 class="font-semibold text-2xl mb-8 text-center">Text Broadcasting System</h1>
                        <div class="py-2">
                            <div>
                                <label for="email">Email</label>
                            </div>
                            <input type="email" placeholder="usep.edu.ph" id="email" class="border rounded border-gray-500 p-1 w-full"></input>
                        </div>
                        <div class="py-2">
                            <div>
                                <label for="password">Password</label>
                            </div>
                            <input type="password" placeholder="**********" id="password" class="border rounded border-gray-500 py-1 px-2 w-full"></input>
                        </div>
                        <div class="flex justify-center items-center">
                            <button class="bg-gradient-to-r from-[#dc7171] to-[#973939] font-semibold text-white px-4 py-2 border rounded w-full">
                                Login
                            </button>
                        </div>
                        <div class="text-center">
                            <p class="py-2">Login using your USeP Email Account</p>
                            <a href="{{ url('auth/google') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-[#dc7171] to-[#973939] text-white font-semibold rounded shadow-md w-full">
                                Login with Google
                            </a>
                        </div>
                    </form>
                @else
                    <div class="text-center">
                        <h1 class="text-2xl font-bold mb-4">Hello! You Don't have permissions to access the system please contact us at email.usep.edu.ph</h1>
                        <img src="{{ Auth::user()->avatar }}" alt="user profile" class="w-24 h-24 rounded-full mx-auto mb-4">
                        <h2 class="text-xl font-semibold">{{ Auth::user()->name }}</h2>
                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                        <a href="{{ url('logout') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75">
                            Logout
                        </a>
                        <div class="mt-4">
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                                    Go to Admin Dashboard
                                </a>
                            @elseif (Auth::user()->role === 'subadmin')
                                <a href="{{ route('subadmin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                                    Go to Subadmin Dashboard
                                </a>
                            @endif
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</body>
</html>
