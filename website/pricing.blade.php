@extends('website.layouts.main')

@section('content')
    <!-- Start pricing -->
    <section class="relative pt-20">
        <div class="relative container mx-auto">
            <div class="absolute mt-0 right-4 lg:left-40 w-32 h-32 md:w-48 md:h-48 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
            <div class="absolute mt-[400px] lg:mt-[500px] -left-4 lg:left-[500px] w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
            <div class="absolute mt-[600px] lg:mt-[700px] left-20 md:left-[300px] lg:left-[600px] w-64 h-64 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 -z-10"></div>
            <div class="relative -mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto max-w-[510px] text-center">
                        <h2 class="text-blue-500 mb-2 block text-lg font-semibold">Confetti comes with hosting</h2>
                        <div class="text-dark mb-4 text-3xl font-bold text-4xl md:text-[40px]">Only pay for hosting</div>
                        <p class="text-body-color text-base font-body font-bold text-balance">
                            Only pay for hosting once your site goes live. With Confetti, you don’t need any additional hosting; we manage all server resources in-house, ensuring transparent and predictable costs.
                        </p>
                    </div>
                </div>
            </div>
            <div class="-mx-4 md:mx-2 flex flex-wrap justify-center pt-8">
                <div class="w-full md:w-1/2 px-4 md:pt-8">
                    <div class="border-blue-500 relative overflow-hidden rounded-xl border border-opacity-20 bg-white p-8">
                        <h2 class="text-blue-500 mb-4 block text-lg font-semibold">
                            Learning, developing and testing
                        </h2>
                        <div class="text-dark mb-5 text-[42px] font-bold">Free</div>
                        <p
                                class="text-body-color text-base font-body font-bold"
                        >
                            Confetti CMS is free to use for tinkering your next idea.
                        </p>
                    </div>
                </div>
                <div class="w-full md:w-1/2 px-4 mb-10 pt-8">
                    <div class="absolute text-center z-10 rotate-6 skew-y-6 right-0 py-2 px-4 bg-blue-500 items-center leading-none rounded-full flex inline-flex text-white shadow-lg">
                        <span class="flex rounded-full border border-opacity-20 border-white uppercase px-2 py-1 text-sm font-bold mr-3">Beta</span>
                        <div class="font-semibold mr-2 text-left flex-col">
                            <div>Since Confetti is in beta</div>
                            <div>all plans are currently free</div>
                        </div>
                    </div>
                    <div class="relative h-full rounded-xl border border-blue-500 border-opacity-20 bg-white mb-16 p-8">
                        <h2 class="text-blue-500 mb-4 block text-lg font-semibold">
                            Personal & small business
                        </h2>
                        <div class="text-dark mb-5 text-[42px] font-bold">
                            €10
                            <span class="text-body-color text-base font-medium"> / month</span>
                        </div>
                        <p class="text-body-color mb-2 pb-2 text-base font-body font-bold">
                            Perfect for a personal website or a small business.
                        </p>
                        <div class="absolute -bottom-16 -right-2 md:-right-4 py-5 px-4 md:px-5 border-blue-500 rounded-xl border border-opacity-20 bg-white">
                            <h2 class="text-blue-500 mb-4 block text-lg font-semibold">
                                High traffic website
                            </h2>
                            <div class="text-dark text-lg font-bold">
                                + €42 <span class="text-body-color text-base font-medium">/ month</span>
                                <div class="text-body-color text-base font-medium">
                                    <div class="flex-col text-base font-body">
                                        <div>for every</div>
                                        <div>additional resource</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mx-4 md:mx-20 mt-10 p-4">
                <h2 class="text-blue-500 mb-4 block text-lg font-semibold">
                    Do you have a high traffic website?
                </h2>
                <p class="text-body-color text-base font-body font-bold">Let us handle the stress for you. So you can scale your business.</p>
                <ul class="mt-3 list-disc list-inside text-gray-500 font-body">
                    <li class="text-body-color mb-1 ">Backups</li>
                    <li class="text-body-color mb-1 ">SSL-certificate</li>
                    <li class="text-body-color mb-1 ">Multiple domains</li>
                    <li class="text-body-color mb-1 ">Private network</li>
                    <li class="text-body-color mb-1 ">Test environments</li>
                    <li class="text-body-color mb-1 ">Secured with Auth0</li>
                </ul>
                <a href="/waiting-list" class="bg-primary border-primary block w-full lg:w-1/2 rounded-md border mt-8 p-4 text-center text-base font-semibold text-white transition hover:bg-opacity-90">
                    Get started
                </a>
            </div>
        </div>
    </section>
    <!-- End pricing -->
    <section class="relative container mx-auto mb-8 bg-white dark:bg-gray-900 dark:text-white pt-20 lg:pt-35 px-4 lg:px-6">
        <h2 class="mb-8 text-4xl tracking-tight font-extrabold md:text-center">Frequently asked questions</h2>
        <div class="grid pt-8 text-left border-t border-gray-200 md:gap-8 dark:border-gray-700 md:grid-cols-2">
            <div class="absolute -mt-[250px] -left-4 md:left-[150px] w-72 h-72 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob "></div>
            <div class="absolute -mt-[50px] left-20 md:left-[250px] w-72 h-72 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            <div class="mb-4">
                <h3 class="flex items-center mb-4 text-lg font-medium">
                    <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    Do you offer a plan for students?
                </h3>
                <p class="text-gray-500 font-body">
                    Our mission is to empower developers of all skill levels to build websites with ease, including students and hobbyists. That's why we offer a free plan for students. And if you want to take your website to the next level and link it to a custom domain name, we offer affordable hosting starting at just €10 per month.
                </p>
            </div>
            <div class="mb-4">
                <h3 class="flex items-center mb-4 text-lg font-medium">
                    <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    Are You Open to Partnering With Us?
                </h3>
                <p class="text-gray-500 font-body">
                    Yes, we would be delighted to partner with you. Whether your company needs assistance with technical challenges or wants to collaborate on building the fundament of new components, we’re here to help.
                </p>
            </div>
            <div class="mb-4">
                <h3 class="flex items-center mb-4 text-lg font-medium">
                    <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    Is Dedicated Support Available?
                </h3>
                <p class="text-gray-500 font-body">
                    Absolutely! We offer dedicated technical support tailored to your company’s needs. Contact us to learn more about how we can help by emailing us at <a href="#" class="js-contact-e text-blue-500"></a>.
                </p>
            </div>
            <div class="mb-4">
                <h3 class="flex items-center mb-4 text-lg font-medium">
                    <svg class="flex-shrink-0 mr-2 w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    Can I reuse code over multiple websites?
                </h3>
                <p class="text-gray-500 font-body">
                    You can clone repositories anywhere in your project. Similar to Git Submodules, but better. Or use Composer to include packages from Packagist.
                </p>
            </div>
        </div>
    </section>
@endsection

