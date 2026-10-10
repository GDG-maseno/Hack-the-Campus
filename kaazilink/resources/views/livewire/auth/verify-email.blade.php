<div class="min-h-screen bg-[#FAFAFA] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-[#1F2937] font-outfit">
            Verify your email
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 font-inter px-4">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if (session('message'))
                <div class="mb-4 font-medium text-sm text-[#006B3F] bg-green-50 p-4 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="resend">
                <div class="mt-4 flex items-center justify-between">
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#006B3F] hover:bg-[#005a34] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006B3F] font-inter transition-colors duration-200">
                        Resend Verification Email
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006B3F] font-inter transition-colors duration-200">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
