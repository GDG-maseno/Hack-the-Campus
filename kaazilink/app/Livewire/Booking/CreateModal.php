<?php

namespace App\Livewire\Booking;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Booking;
use App\Models\ServiceArea;
use App\Data\ServiceDirectory;
use Illuminate\Support\Facades\Auth;

class CreateModal extends Component
{
    public $providerId;
    public $isOpen = false;
    
    public $serviceSlug = '';
    public $serviceAreaId = '';
    public $scheduledAt = '';

    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'serviceSlug' => 'required|string',
        'serviceAreaId' => 'required|exists:service_areas,id',
        'scheduledAt' => 'nullable|date|after:now',
    ];

    #[On('openBookingModal')]
    public function openModal($providerId, $serviceSlug = null)
    {
        $this->providerId = $providerId;
        $this->isOpen = true;
        $this->reset(['serviceSlug', 'serviceAreaId', 'scheduledAt', 'successMessage', 'errorMessage']);
        $this->resetValidation();

        $provider = User::find($providerId);
        if ($provider) {
            if ($serviceSlug) {
                $this->serviceSlug = $serviceSlug;
            } else {
                $firstService = $provider->providerServices->first();
                if ($firstService) {
                    $this->serviceSlug = $firstService->service_slug;
                }
            }
            $firstArea = $provider->serviceAreas->first();
            if ($firstArea) {
                $this->serviceAreaId = $firstArea->id;
            }
        }

        $this->dispatch('wirekit-modal-show', name: 'booking-create-modal');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->dispatch('wirekit-modal-close', name: 'booking-create-modal');
    }

    public function submit()
    {
        $this->reset(['successMessage', 'errorMessage']);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            // Gracefully resolve name to numeric ID if text name was submitted
            if ($this->serviceAreaId && !is_numeric($this->serviceAreaId)) {
                $area = ServiceArea::where('name', $this->serviceAreaId)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($this->serviceAreaId))])
                    ->first();
                if ($area) {
                    $this->serviceAreaId = $area->id;
                }
            }

            // Gracefully resolve service name to slug if service name was submitted
            if ($this->serviceSlug) {
                $allServices = collect(ServiceDirectory::getServices());
                $foundService = $allServices->first(function($s) {
                    return strtolower($s['name']) === strtolower(trim($this->serviceSlug))
                        || strtolower($s['slug']) === strtolower(trim($this->serviceSlug));
                });
                if ($foundService) {
                    $this->serviceSlug = $foundService['slug'];
                }
            }

            $this->validate();

            $provider = User::approvedProvider()->find($this->providerId);
            if (!$provider) {
                $this->errorMessage = 'Something went wrong. Please try again.';
                return;
            }
            
            // Ensure provider offers this service
            if (!$provider->providerServices()->where('service_slug', $this->serviceSlug)->exists()) {
                $this->addError('serviceSlug', 'The provider does not offer this service.');
                $this->errorMessage = 'Something went wrong. Please try again.';
                return;
            }

            // Ensure provider serves this area
            if (!$provider->serviceAreas()->where('service_area_id', $this->serviceAreaId)->exists()) {
                $this->addError('serviceAreaId', 'The provider does not serve this area.');
                $this->errorMessage = 'Something went wrong. Please try again.';
                return;
            }

            $providerService = $provider->providerServices()
                ->where('service_slug', $this->serviceSlug)
                ->first();

            if (!$providerService || $providerService->price <= 0) {
                $this->addError('serviceSlug', 'This provider has not set a price for the selected service. Please contact them directly.');
                $this->errorMessage = 'Something went wrong. Please try again.';
                return;
            }

            $booking = Booking::create([
                'client_id' => Auth::id(),
                'provider_id' => $provider->id,
                'service_slug' => $this->serviceSlug,
                'price' => $providerService->price,
                'scheduled_at' => $this->scheduledAt ?: null,
                'status' => 'requested',
            ]);

            // Send email notification to provider
            $provider->notify(new \App\Notifications\NewBookingRequestNotification($booking));

            $msg = 'Booking request sent! You can track it in My Bookings.';
            $this->successMessage = $msg;
            session()->flash('success', $msg);

            // Dispatch success toast & booking created event
            $this->dispatch('booking-success-toast', message: $msg);
            $this->dispatch('bookingCreated');

            // Dispatch delayed modal close trigger (800ms)
            $this->dispatch('schedule-modal-close');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = 'Something went wrong. Please try again.';
            throw $e;
        } catch (\Throwable $e) {
            $this->errorMessage = 'Something went wrong. Please try again.';
        }
    }

    public function render()
    {
        $provider = $this->providerId ? User::find($this->providerId) : null;
        
        $services = [];
        $areas = [];
        
        if ($provider) {
            $services = $provider->providerServices->map(function($ps) {
                return [
                    'value' => $ps->service_slug,
                    'label' => $ps->display_name . ' (' . $ps->formatted_price . ')',
                ];
            })->toArray();
            
            $areas = $provider->serviceAreas->map(function($area) {
                return ['value' => $area->id, 'label' => $area->name];
            })->toArray();
        }

        return view('livewire.booking.create-modal', [
            'provider' => $provider,
            'servicesOptions' => $services,
            'areasOptions' => $areas,
        ]);
    }
}
