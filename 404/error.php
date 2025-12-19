<?php
http_response_code(404);

$errorCode = $_GET['code'] ?? '404';
$message   = $_GET['msg']  ?? 'The page you are looking for does not exist or may have been moved.';
$backUrl   = $_GET['back'] ?? '/admin/installations_register.php';

$errors = [
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '403' => 'Access Denied',
    '404' => 'Page Not Found',
    '500' => 'Internal Server Error'
];

$title = $errors[$errorCode] ?? 'Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Animations -->
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-up {
            animation: fadeUp 0.8s ease-out forwards;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center px-4">

<div class="max-w-xl w-full bg-white rounded-2xl shadow-xl p-8 text-center fade-up">

    <!-- GIF -->
    <div class="flex justify-center mb-4">
        <video
            class="w-56 max-w-full float"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
        >
            <source src="./404%20Error.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>


    <!-- Error Code -->
    <div class="text-6xl font-extrabold text-red-500 mb-2">
        <?= htmlspecialchars($errorCode) ?>
    </div>

    <!-- Title -->
    <h1 class="text-2xl font-semibold text-slate-800 mb-3">
        <?= htmlspecialchars($title) ?>
    </h1>

    <!-- Message -->
    <p class="text-slate-600 leading-relaxed mb-6">
        <?= htmlspecialchars($message) ?>
    </p>

    <!-- Divider -->
    <div class="h-px bg-slate-200 my-6"></div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="<?= htmlspecialchars($backUrl) ?>"
           class="inline-flex items-center justify-center px-6 py-2.5
                  bg-slate-800 text-white rounded-lg
                  transition-all duration-300
                  hover:bg-slate-900 hover:scale-105">
            ← Go Back
        </a>

        <a href="/admin/installations_register.php"
           class="inline-flex items-center justify-center px-6 py-2.5
                  border border-slate-300 text-slate-700 rounded-lg
                  transition-all duration-300
                  hover:bg-slate-100 hover:scale-105">
            Dashboard
        </a>
    </div>

    <!-- Footer -->
    <p class="mt-8 text-xs text-slate-400">
        Error logged • Please contact admin if this persists
    </p>

</div>

</body>
</html>
