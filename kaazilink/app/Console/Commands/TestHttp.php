<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:test-http')]
#[Description('Command description')]
class TestHttp extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 4c: A check confirming the protected document route returns 403 when accessed by non-admin, non-owner
        $user = \App\Models\User::factory()->create(['role' => 'client']);
        \Illuminate\Support\Facades\Auth::login($user);

        $request = \Illuminate\Http\Request::create('/kyc/document/some/path.jpg', 'GET');
        $response = app()->handle($request);
        $this->info("4c: GET /kyc/document/some/path.jpg as non-owner -> HTTP " . $response->getStatusCode());

        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();

        // 4d: A real request to the unapproved test provider's direct profile URL showing HTTP 404
        $unapproved = \App\Models\User::factory()->create(['role' => 'provider', 'kyc_status' => 'pending']);
        $request2 = \Illuminate\Http\Request::create('/provider/' . $unapproved->id, 'GET');
        $response2 = app()->handle($request2);
        $this->info("4d: GET /provider/{$unapproved->id} (unapproved provider) -> HTTP " . $response2->getStatusCode());
        
        $unapproved->delete();
    }
}
