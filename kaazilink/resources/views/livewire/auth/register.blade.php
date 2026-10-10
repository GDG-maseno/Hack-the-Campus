<div class="w-full flex-1 flex flex-col justify-center py-12 sm:px-6 lg:px-8" x-data="{ agreedTerms: false, termsNotice: false }">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-[#1F2937] font-outfit">
            Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 font-inter">
            Or
            <a href="{{ route('login') }}" class="font-medium text-[#006B3F] hover:text-[#005a34]">
                sign in to your existing account
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if(session()->has('error'))
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ session('error') }}</h3>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="register" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 font-inter">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input wire:model="name" id="name" name="name" type="text" autocomplete="name" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                    </div>
                    @error('name') <span class="text-xs text-[#C0392B] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 font-inter">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                    </div>
                    @error('email') <span class="text-xs text-[#C0392B] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 font-inter">
                        Password
                    </label>
                    <div class="mt-1">
                        <input wire:model="password" id="password" name="password" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                    </div>
                    @error('password') <span class="text-xs text-[#C0392B] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 font-inter">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#006B3F] focus:border-[#006B3F] sm:text-sm">
                    </div>
                </div>

                {{-- Terms and Conditions Checkbox --}}
                <div class="pt-2">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input wire:model="terms" id="terms" name="terms" type="checkbox"
                                @change="agreedTerms = $event.target.checked"
                                class="h-4 w-4 text-[#006B3F] focus:ring-[#006B3F] border-gray-300 rounded transition duration-150 ease-in-out cursor-pointer">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700 font-inter cursor-pointer">
                                I agree to the 
                                <a href="{{ route('terms') }}" target="_blank" class="text-[#006B3F] underline hover:text-[#005a34]">Terms of Service</a>
                                and 
                                <a href="{{ route('privacy') }}" target="_blank" class="text-[#006B3F] underline hover:text-[#005a34]">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    @error('terms') <span class="text-xs text-[#C0392B] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#006B3F] hover:bg-[#005a34] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006B3F] font-inter transition-colors duration-200">
                        Sign up
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500 font-inter">
                            Or continue with
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('auth.google') }}"
                        @click="if(!agreedTerms) { $event.preventDefault(); termsNotice = true; }"
                        :class="agreedTerms ? 'opacity-100 hover:bg-gray-50 cursor-pointer' : 'opacity-60 cursor-not-allowed'"
                        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 font-inter transition-colors duration-200">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span class="ml-2">Google</span>
                    </a>
                    
                    <p x-show="termsNotice && !agreedTerms" x-transition class="text-xs text-[#C0392B] mt-2 text-center" style="display: none;">
                        Please agree to the Terms of Service & Privacy Policy before signing up with Google.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
