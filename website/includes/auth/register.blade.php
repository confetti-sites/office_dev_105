<!-- You can test the result of this file in the CMS, but you need to copy the content of the file and paste it in Auth0 (Universal Login -> Advanced Options -> Login) -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Waitlist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- <link rel="stylesheet" href="/resources/website__tailwind/tailwind.output.css"/>--}}-->
    <!-- Tailwind css: -->
    <style>
        *, ::before, ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x:  ;
            --tw-pan-y:  ;
            --tw-pinch-zoom:  ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position:  ;
            --tw-gradient-via-position:  ;
            --tw-gradient-to-position:  ;
            --tw-ordinal:  ;
            --tw-slashed-zero:  ;
            --tw-numeric-figure:  ;
            --tw-numeric-spacing:  ;
            --tw-numeric-fraction:  ;
            --tw-ring-inset:  ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur:  ;
            --tw-brightness:  ;
            --tw-contrast:  ;
            --tw-grayscale:  ;
            --tw-hue-rotate:  ;
            --tw-invert:  ;
            --tw-saturate:  ;
            --tw-sepia:  ;
            --tw-drop-shadow:  ;
            --tw-backdrop-blur:  ;
            --tw-backdrop-brightness:  ;
            --tw-backdrop-contrast:  ;
            --tw-backdrop-grayscale:  ;
            --tw-backdrop-hue-rotate:  ;
            --tw-backdrop-invert:  ;
            --tw-backdrop-opacity:  ;
            --tw-backdrop-saturate:  ;
            --tw-backdrop-sepia:  ;
            --tw-contain-size:  ;
            --tw-contain-layout:  ;
            --tw-contain-paint:  ;
            --tw-contain-style:  ;
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x:  ;
            --tw-pan-y:  ;
            --tw-pinch-zoom:  ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position:  ;
            --tw-gradient-via-position:  ;
            --tw-gradient-to-position:  ;
            --tw-ordinal:  ;
            --tw-slashed-zero:  ;
            --tw-numeric-figure:  ;
            --tw-numeric-spacing:  ;
            --tw-numeric-fraction:  ;
            --tw-ring-inset:  ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur:  ;
            --tw-brightness:  ;
            --tw-contrast:  ;
            --tw-grayscale:  ;
            --tw-hue-rotate:  ;
            --tw-invert:  ;
            --tw-saturate:  ;
            --tw-sepia:  ;
            --tw-drop-shadow:  ;
            --tw-backdrop-blur:  ;
            --tw-backdrop-brightness:  ;
            --tw-backdrop-contrast:  ;
            --tw-backdrop-grayscale:  ;
            --tw-backdrop-hue-rotate:  ;
            --tw-backdrop-invert:  ;
            --tw-backdrop-opacity:  ;
            --tw-backdrop-saturate:  ;
            --tw-backdrop-sepia:  ;
            --tw-contain-size:  ;
            --tw-contain-layout:  ;
            --tw-contain-paint:  ;
            --tw-contain-style:  ;
        }

        /*
        ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com
        */

        /*
        1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
        2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
        */

        *,
        ::before,
        ::after {
            box-sizing: border-box;
            /* 1 */
            border-width: 0;
            /* 2 */
            border-style: solid;
            /* 2 */
            border-color: #e5e7eb;
            /* 2 */
        }

        ::before,
        ::after {
            --tw-content: '';
        }

        /*
        1. Use a consistent sensible line-height in all browsers.
        2. Prevent adjustments of font size after orientation changes in iOS.
        3. Use a more readable tab size.
        4. Use the user's configured `sans` font-family by default.
        5. Use the user's configured `sans` font-feature-settings by default.
        6. Use the user's configured `sans` font-variation-settings by default.
        7. Disable tap highlights on iOS
        */

        html,
        :host {
            line-height: 1.5;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
            -moz-tab-size: 4;
            /* 3 */
            -o-tab-size: 4;
            tab-size: 4;
            /* 3 */
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            /* 4 */
            font-feature-settings: normal;
            /* 5 */
            font-variation-settings: normal;
            /* 6 */
            -webkit-tap-highlight-color: transparent;
            /* 7 */
        }

        /*
        1. Remove the margin in all browsers.
        2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
        */

        body {
            margin: 0;
            /* 1 */
            line-height: inherit;
            /* 2 */
        }

        /*
        1. Add the correct height in Firefox.
        2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
        3. Ensure horizontal rules are visible by default.
        */

        hr {
            height: 0;
            /* 1 */
            color: inherit;
            /* 2 */
            border-top-width: 1px;
            /* 3 */
        }

        /*
        Add the correct text decoration in Chrome, Edge, and Safari.
        */

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted;
        }

        /*
        Remove the default font size and weight for headings.
        */

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit;
        }

        /*
        Reset links to optimize for opt-in styling instead of opt-out.
        */

        a {
            color: inherit;
            text-decoration: inherit;
        }

        /*
        Add the correct font weight in Edge and Safari.
        */

        b,
        strong {
            font-weight: bolder;
        }

        /*
        1. Use the user's configured `mono` font-family by default.
        2. Use the user's configured `mono` font-feature-settings by default.
        3. Use the user's configured `mono` font-variation-settings by default.
        4. Correct the odd `em` font sizing in all browsers.
        */

        code,
        kbd,
        samp,
        pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            /* 1 */
            font-feature-settings: normal;
            /* 2 */
            font-variation-settings: normal;
            /* 3 */
            font-size: 1em;
            /* 4 */
        }

        /*
        Add the correct font size in all browsers.
        */

        small {
            font-size: 80%;
        }

        /*
        Prevent `sub` and `sup` elements from affecting the line height in all browsers.
        */

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        /*
        1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
        2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
        3. Remove gaps between table borders by default.
        */

        table {
            text-indent: 0;
            /* 1 */
            border-color: inherit;
            /* 2 */
            border-collapse: collapse;
            /* 3 */
        }

        /*
        1. Change the font styles in all browsers.
        2. Remove the margin in Firefox and Safari.
        3. Remove default padding in all browsers.
        */

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            /* 1 */
            font-feature-settings: inherit;
            /* 1 */
            font-variation-settings: inherit;
            /* 1 */
            font-size: 100%;
            /* 1 */
            font-weight: inherit;
            /* 1 */
            line-height: inherit;
            /* 1 */
            letter-spacing: inherit;
            /* 1 */
            color: inherit;
            /* 1 */
            margin: 0;
            /* 2 */
            padding: 0;
            /* 3 */
        }

        /*
        Remove the inheritance of text transform in Edge and Firefox.
        */

        button,
        select {
            text-transform: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Remove default button styles.
        */

        button,
        input:where([type='button']),
        input:where([type='reset']),
        input:where([type='submit']) {
            -webkit-appearance: button;
            /* 1 */
            background-color: transparent;
            /* 2 */
            background-image: none;
            /* 2 */
        }

        /*
        Use the modern Firefox focus style for all focusable elements.
        */

        :-moz-focusring {
            outline: auto;
        }

        /*
        Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
        */

        :-moz-ui-invalid {
            box-shadow: none;
        }

        /*
        Add the correct vertical alignment in Chrome and Firefox.
        */

        progress {
            vertical-align: baseline;
        }

        /*
        Correct the cursor style of increment and decrement buttons in Safari.
        */

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto;
        }

        /*
        1. Correct the odd appearance in Chrome and Safari.
        2. Correct the outline style in Safari.
        */

        [type='search'] {
            -webkit-appearance: textfield;
            /* 1 */
            outline-offset: -2px;
            /* 2 */
        }

        /*
        Remove the inner padding in Chrome and Safari on macOS.
        */

        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Change font properties to `inherit` in Safari.
        */

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            /* 1 */
            font: inherit;
            /* 2 */
        }

        /*
        Add the correct display in Chrome and Safari.
        */

        summary {
            display: list-item;
        }

        /*
        Removes the default spacing and border for appropriate elements.
        */

        blockquote,
        dl,
        dd,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        figure,
        p,
        pre {
            margin: 0;
        }

        fieldset {
            margin: 0;
            padding: 0;
        }

        legend {
            padding: 0;
        }

        ol,
        ul,
        menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        /*
        Reset default styling for dialogs.
        */

        dialog {
            padding: 0;
        }

        /*
        Prevent resizing textareas horizontally by default.
        */

        textarea {
            resize: vertical;
        }

        /*
        1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
        2. Set the default placeholder color to the user's configured gray 400 color.
        */

        input::-moz-placeholder, textarea::-moz-placeholder {
            opacity: 1;
            /* 1 */
            color: #9ca3af;
            /* 2 */
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            /* 1 */
            color: #9ca3af;
            /* 2 */
        }

        /*
        Set the default cursor for buttons.
        */

        button,
        [role="button"] {
            cursor: pointer;
        }

        /*
        Make sure disabled buttons don't get the pointer cursor.
        */

        :disabled {
            cursor: default;
        }

        /*
        1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
        2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
           This can trigger a poorly considered lint error in some tools but is included by design.
        */

        img,
        svg,
        video,
        canvas,
        audio,
        iframe,
        embed,
        object {
            display: block;
            /* 1 */
            vertical-align: middle;
            /* 2 */
        }

        /*
        Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
        */

        img,
        video {
            max-width: 100%;
            height: auto;
        }

        /* Make elements with the HTML hidden attribute stay hidden by default */

        [hidden]:where(:not([hidden="until-found"])) {
            display: none;
        }

        /* width */

        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */

        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .container {
            width: 100%;
        }

        @media (min-width: 640px) {
            .container {
                max-width: 640px;
            }
        }

        @media (min-width: 768px) {
            .container {
                max-width: 768px;
            }
        }

        @media (min-width: 1024px) {
            .container {
                max-width: 1024px;
            }
        }

        @media (min-width: 1280px) {
            .container {
                max-width: 1280px;
            }
        }

        @media (min-width: 1536px) {
            .container {
                max-width: 1536px;
            }
        }

        .absolute {
            position: absolute;
        }

        .-left-4 {
            left: -1rem;
        }

        .bottom-\[30rem\] {
            bottom: 30rem;
        }

        .bottom-\[40rem\] {
            bottom: 40rem;
        }

        .bottom-\[7rem\] {
            bottom: 7rem;
        }

        .left-20 {
            left: 5rem;
        }

        .right-0 {
            right: 0px;
        }

        .-z-10 {
            z-index: -10;
        }

        .m-auto {
            margin: auto;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .block {
            display: block;
        }

        .flex {
            display: flex;
        }

        .h-64 {
            height: 16rem;
        }

        .h-72 {
            height: 18rem;
        }

        .h-screen {
            height: 100vh;
        }

        .w-64 {
            width: 16rem;
        }

        .w-72 {
            width: 18rem;
        }

        .w-96 {
            width: 24rem;
        }

        .w-full {
            width: 100%;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .border {
            border-width: 1px;
        }

        .border-2 {
            border-width: 2px;
        }

        .border-primary {
            --tw-border-opacity: 1;
            border-color: rgb(214 144 81 / var(--tw-border-opacity, 1));
        }

        .bg-blue-300 {
            --tw-bg-opacity: 1;
            background-color: rgb(147 197 253 / var(--tw-bg-opacity, 1));
        }

        .bg-green-300 {
            --tw-bg-opacity: 1;
            background-color: rgb(134 239 172 / var(--tw-bg-opacity, 1));
        }

        .bg-primary {
            --tw-bg-opacity: 1;
            background-color: rgb(214 144 81 / var(--tw-bg-opacity, 1));
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1));
        }

        .bg-yellow-100 {
            --tw-bg-opacity: 1;
            background-color: rgb(254 249 195 / var(--tw-bg-opacity, 1));
        }

        .p-6 {
            padding: 1.5rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .text-center {
            text-align: center;
        }

        .font-body {
            font-family: sans-serif;
        }

        .text-3xl {
            font-size: 1.875rem;
            line-height: 2.25rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity, 1));
        }

        .text-gray-800 {
            --tw-text-opacity: 1;
            color: rgb(31 41 55 / var(--tw-text-opacity, 1));
        }

        .text-red-500 {
            --tw-text-opacity: 1;
            color: rgb(239 68 68 / var(--tw-text-opacity, 1));
        }

        .text-white {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity, 1));
        }

        .opacity-70 {
            opacity: 0.7;
        }

        .mix-blend-multiply {
            mix-blend-mode: multiply;
        }

        .shadow-lg {
            --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .blur-xl {
            --tw-blur: blur(24px);
            filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
        }

        .filter {
            filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow);
        }

        @keyframes slide-in-left {
            0% {
                transform: translateX(-50px);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-in-right {
            0% {
                transform: translateX(50px);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-in-top {
            0% {
                transform: translateY(50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        :root {
            font-family: "pluto";
        }

        .container {
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 640px) {
            .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        @media (min-width: 1280px) {
            .container {
                padding-left: 0px;
                padding-right: 0px;
                max-width: 72rem;
            }
        }

        input {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity, 1));
        }

        .button {
            border-radius: 0.25rem;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            --tw-text-opacity: 1;
            color: rgb(34 36 40 / var(--tw-text-opacity, 1));
        }

        .button-primary {
            --tw-bg-opacity: 1;
            background-color: rgb(214 144 81 / var(--tw-bg-opacity, 1));
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity, 1));
        }

        .button-primary:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(214 144 81 / var(--tw-bg-opacity, 1));
        }

        .button-clear {
            --tw-text-opacity: 1;
            color: rgb(34 36 40 / var(--tw-text-opacity, 1));
        }

        .button-clear:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity, 1));
            --tw-text-opacity: 1;
            color: rgb(34 36 40 / var(--tw-text-opacity, 1));
        }

        .button-round {
            border-radius: 9999px;
        }

        .button-ghost {
            border-width: 2px;
            --tw-border-opacity: 1;
            border-color: rgb(214 144 81 / var(--tw-border-opacity, 1));
            --tw-text-opacity: 1;
            color: rgb(34 36 40 / var(--tw-text-opacity, 1));
        }

        .button-ghost:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(214 144 81 / var(--tw-bg-opacity, 1));
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity, 1));
        }

        .button-icon {
            display: flex;
            align-items: center;
        }

        .button-icon-only {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }

        .button-icon-only .icon {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>
    <style>
        @font-face {
            font-family: "pluto";
            src: url("/website/assets/fonts/plutomedium-webfont.woff2")
            format("woff2"),
            url("/website/assets/fonts/plutomedium-webfont.woff") format("woff");
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: "pluto";
            src: url("/website/assets/fonts/plutoregular-webfont.woff2")
            format("woff2"),
            url("/website/assets/fonts/plutoregular-webfont.woff") format("woff");
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: "pluto";
            src: url("/website/assets/fonts/plutothin-webfont.woff2") format("woff2"),
            url("/website/assets/fonts/plutothin-webfont.woff") format("woff");
            font-weight: 100;
            font-style: normal;
        }
    </style>
    <!--<link rel="stylesheet" href="/website/assets/css/fonts.css"/>-->
</head>
<body>

<div class="container flex h-screen font-body">
    <div class="absolute bottom-[40rem] -left-4 w-64 h-64 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute bottom-[30rem] right-0 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="absolute bottom-[7rem] left-20 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
    <div class="m-auto w-96 p-6 border rounded-lg shadow-lg bg-white">
        <h1 class="text-3xl font-semibold text-gray-800 text-center">Waitlist</h1>
        <div class="mt-4 text-gray-800">
            <form onsubmit="return false;" method="post">
                <p class="text-gray-600 font-body">
                    In the next step, we’ll request access to your email address. This is needed to send you an invitation. We’ll only email you regarding your invitation.
                </p>
                <button type="button"
                        id="btn-github"
                        class="w-full mt-4 border-2 border-primary bg-primary text-white px-6 py-3 rounded-lg">
                    Put me on the list
                </button>
            </form>
            <div id="error-message" class="text-red-500 mt-2 text-center"></div>
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
