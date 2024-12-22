<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a New Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 shadow-lg rounded-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-white mb-6">Create a New Account</h2>

        <form method="POST" action="register_process.php">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">E-mail</label>
                <input type="email" id="email" name="email" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" id="password" name="password" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
                <button type="button" onclick="togglePasswordVisibility('password')" 
                        class="absolute right-3 top-9 text-gray-400 hover:text-gray-200">
                    👁️
                </button>
            </div>
            <div class="mb-4 relative">
                <label for="confirm_password" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
                <button type="button" onclick="togglePasswordVisibility('confirm_password')" 
                        class="absolute right-3 top-9 text-gray-400 hover:text-gray-200">
                    👁️
                </button>
            </div>
            <div class="mb-4">
                <label for="security_question" class="block text-sm font-medium text-gray-300">Security Question</label>
                <input type="text" id="security_question" name="security_question" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4">
                <label for="security_answer" class="block text-sm font-medium text-gray-300">Security Answer</label>
                <input type="text" id="security_answer" name="security_answer" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-700 bg-gray-700 text-white rounded-md focus:ring-blue-500 focus:border-blue-500" 
                       required>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" id="enable_2fa" name="enable_2fa" 
                       class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500 bg-gray-700">
                <label for="enable_2fa" class="ml-2 text-sm text-gray-300">Enable 2FA</label>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                Continue
            </button>
        </form>
        <p class="text-center text-sm text-gray-400 mt-4">
            Already have an account? <a href="login.php" class="text-blue-400 hover:underline">Sign In</a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
