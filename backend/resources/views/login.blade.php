<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Auth</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="flex justify-center items-center h-screen bg-gray-100">
    {{-- Question from  --}}
    <form action="question" method="post" class="bg-gray-500 border">
        @csrf
        <input type="text" name="title" placeholder="Your Question here" class="bg-white">
        <input type="submit" name="submit" id="">
    </form>




    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <div class="text-center mb-4">
            <button id="showLogin" class="text-blue-500 font-bold">Login</button> |
            <button id="showRegister" class="text-blue-500 font-bold">Register</button>
        </div>
        
        <!-- Login Form -->
        <form id="loginForm" class="space-y-4">
            <h2 class="text-xl font-bold">Login</h2>
            <label for="loginEmail" class="block">Email:</label>
            <input type="email" id="loginEmail" name="email" class="w-full p-2 border rounded">
            
            <label for="loginPassword" class="block">Password:</label>
            <input type="password" id="loginPassword" name="password" class="w-full p-2 border rounded">
            
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
        </form>
        
        <!-- Register Form -->
        <form id="registerForm" class="space-y-4 hidden">
            <h2 class="text-xl font-bold">Register</h2>
            <label for="name" class="block">Name:</label>
            <input type="text" id="name" name="name" class="w-full p-2 border rounded">
            
            <label for="email" class="block">Email:</label>
            <input type="email" id="email" name="email" class="w-full p-2 border rounded">
            
            <label for="password" class="block">Password:</label>
            <input type="password" id="password" name="password" class="w-full p-2 border rounded">
            
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Register</button>
        </form>
        
        <div id="responseMessage" class="mt-4 text-center"></div>
    </div>

    <script>
        $(document).ready(function() {
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Toggle between login and register form
            $('#showLogin').click(function() {
                $('#loginForm').removeClass('hidden');
                $('#registerForm').addClass('hidden');
            });
            
            $('#showRegister').click(function() {
                $('#registerForm').removeClass('hidden');
                $('#loginForm').addClass('hidden');
            });

            // Register User
            $('#registerForm').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: "/api/register",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        name: $('#name').val(),
                        email: $('#email').val(),
                        password: $('#password').val()
                    }),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#responseMessage').html('<p class="text-green-500">' + response.message + '</p>');
                        $('#registerForm')[0].reset();
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || "Registration failed!";
                        $('#responseMessage').html('<p class="text-red-500">Error: ' + errorMessage + '</p>');
                    }
                });
            });

            // Login User
            $('#loginForm').submit(function(event) {
    event.preventDefault();
    
    $.ajax({
        url: "{{ url('/api/userLogin') }}",
        type: "POST",
        data: {
            email: $('#loginEmail').val(),
            password: $('#loginPassword').val()
        },
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                sessionStorage.setItem('user_name', response.user.name);
                sessionStorage.setItem('auth_token', response.token); // Store token

                window.location.href = "{{ url('/api/index') }}";
            } else {
                $('#responseMessage').html('<p class="text-red-500">Error: ' + response.message + '</p>');
            }
        },
        error: function(xhr) {
            $('#responseMessage').html('<p class="text-red-500">Error: ' + xhr.responseJSON.message + '</p>');
        }
    });
});


        });
    </script>
</body>
</html>
