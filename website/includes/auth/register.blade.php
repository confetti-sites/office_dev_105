<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Waitlist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="/resources/website__tailwind/tailwind.output.css"/>
    <link rel="stylesheet" href="/website/assets/css/fonts.css"/>
</head>
<body>

<div class="flex h-screen">
    <div class="m-auto">
        <h1 class="text-3xl font-semibold text-gray-800">Waitlist</h1>
        <div class="mt-4 text-gray-800">
            <form onsubmit="return false;" method="post">
                <p class="text-blue-400 font-body">In the next step, we’ll request access to your email address. This is needed to send you an invitation. We’ll only email you regarding your invitation.</p>
                <button type="button"
                        id="btn-github"
                        class="inline-block border-2 border-primary bg-primary text-white px-6 py-3 rounded-lg">
                    Put me on the list
                </button>
            </form>
            <div id="error-message" class="text-red-500 mt-2"></div>
        </div>
    </div>
</div>

<!--[if IE 8]>
<script src="//cdnjs.cloudflare.com/ajax/libs/ie8/0.2.5/ie8.js"></script>
<![endif]-->

<!--[if lte IE 9]>
<script src="https://cdn.auth0.com/js/polyfills/1.0/base64.min.js"></script>
<script src="https://cdn.auth0.com/js/polyfills/1.0/es5-shim.min.js"></script>
<![endif]-->

<script src="https://cdn.auth0.com/js/auth0/9.28/auth0.min.js"></script>
<script src="https://cdn.auth0.com/js/polyfills/1.0/object-assign.min.js"></script>
<script>
    window.addEventListener('load', function () {

        var config = JSON.parse(
            decodeURIComponent(escape(window.atob('@@config@@')))
        );

        var leeway = config.internalOptions.leeway;
        if (leeway) {
            var convertedLeeway = parseInt(leeway);

            if (!isNaN(convertedLeeway)) {
                config.internalOptions.leeway = convertedLeeway;
            }
        }

        var params = {
            overrides: {
                __tenant: config.auth0Tenant,
                __token_issuer: config.authorizationServer.issuer
            },
            domain: config.auth0Domain,
            clientID: config.clientID,
            redirectUri: config.callbackURL,
            responseType: 'code',
            scope: config.internalOptions.scope,
            _csrf: config.internalOptions._csrf,
            state: config.internalOptions.state,
            _intstate: config.internalOptions._intstate
        };

        var triggerCaptcha = null;
        var signupCaptcha = null;
        var webAuth = new auth0.WebAuth(params);
        var databaseConnection = 'Username-Password-Authentication';
        var captcha = webAuth.renderCaptcha(
            document.querySelector('.captcha-container'),
            null,
            (error, payload) => {
                if (payload) {
                    triggerCaptcha = payload.triggerCaptcha;
                }
            }
        );


        function loginWithGithub() {
            webAuth.authorize({
                connection: 'github'
            }, function (err) {
                if (err) displayError(err);
            });
        }

        function displayError(err) {
            captcha.reload();
            var errorMessage = document.getElementById('error-message');
            errorMessage.innerText = err.policy || err.description;
            errorMessage.style.display = 'block';
        }

        document.getElementById('btn-github').addEventListener('click', loginWithGithub);
    });
</script>
</body>
</html>
