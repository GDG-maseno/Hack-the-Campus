<div>
    <h1 class="text-3xl font-bold mb-6 text-[#006B3F]">Dashboard Overview</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-[#006B3F]">
            <h2 class="text-lg font-semibold text-gray-700">Pending KYC</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::where('kyc_status', 'pending')->count() }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-[#E8A317]">
            <h2 class="text-lg font-semibold text-gray-700">Approved Providers</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::approvedProvider()->count() }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-[#C0392B]">
            <h2 class="text-lg font-semibold text-gray-700">Total Clients</h2>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::where('role', 'client')->count() }}</p>
        </div>
    </div>
</div>
