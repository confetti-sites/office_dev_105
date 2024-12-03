<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>Page not found</title>
    <link rel="stylesheet" href="/resources/website__tailwind/tailwind.output.css"/>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%2210 0 100 100%22><text y=%22.90em%22 font-size=%2290%22>🤷</text></svg>"></link>
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            line-height: 1.5;
            word-break: break-word;
            background-color: #f8f8f8fa;
        }

        @media (max-width: 600px) {
            .hide_on_mobile {
                display: none;
            }
        }

        @keyframes blob {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 0;
            }
            50% {
                opacity: 0.7;
            }
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            mix-blend-mode: multiply;
            filter: blur(1rem);
        }

        .blob_1 {
            top: 5rem;
            left: -1rem;
            width: 18rem;
            height: 18rem;
            background-color: rgb(0, 255, 0);
            opacity: 0.7;
            animation: blob 5s infinite;
        }

        .blob_2 {
            top: 0;
            right: 0;
            width: 18rem;
            height: 18rem;
            background-color: rgb(0, 255, 0);
            opacity: 0.7;
            animation: blob 5s infinite;
        }

        .blob_3 {
            bottom: 0;
            left: 0;
            width: 18rem;
            height: 18rem;
            background-color: rgb(0, 255, 0
            );
            opacity: 0.7;
            animation: blob 5s infinite;
        }

        .blob_4 {
            bottom: 0;
            right: 0;
            width: 18rem;
            height: 18rem;
            background-color: rgb(0, 255, 0);
            opacity: 0.7;
            animation: blob 5s infinite;
        }
    </style>
</head>
<body>

<div class="flex items-center justify-center w-full h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center w-full h-full space-y-4">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200">404</h1>
        <p class="text-lg text-gray-600 dark:text-gray-300">Page not found</p>
        <a href="/" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primaryLight">Go to home</a>
    </div>
</div>
</body>
</html>