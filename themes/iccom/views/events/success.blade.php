@extends('iccom::layouts.app')

@section('title', 'Registration Successful - iCCom Indonesia Cloud Community')

@section('content')
    <div style="height: 100px;"></div>

    @php
        // Retrieve registration details from session or query param
        $reg   = null;
        $event = null;
        if (request('email') && request('slug')) {
            $reg = \Plugins\Events\Models\EventRegistration::where('email', request('email'))
                ->whereHas('event', fn($q) => $q->where('slug', request('slug')))
                ->with('event')
                ->latest()
                ->first();
            $event = $reg?->event;
        }
    @endphp

    <!-- Success Hero -->
    <section class="success-section d-flex align-items-center position-relative text-white text-center">
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-7" data-aos="zoom-in">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5" class="text-white opacity-75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    @if($event)
                        <h1 class="display-5 fw-bold mb-2">
                            {{ $event->success_title ?: 'You\'re All Set!' }}
                        </h1>
                        <p class="lead mb-4 text-white-50">
                            {{ $event->success_desc ?: 'Your registration has been submitted. We\'ll send a confirmation email shortly.' }}
                        </p>
                    @else
                        <h1 class="display-4 fw-bold mb-4">Thank you for registering!</h1>
                        <p class="lead mb-5 text-white-50">Your event registration has been submitted successfully.</p>
                    @endif

                    <!-- Registration Card -->
                    @if($reg)
                    <div class="card border-0 shadow-lg rounded-4 text-start mb-5 mx-auto" style="max-width: 540px;">
                        <div class="card-body p-4">
                            <!-- Status Badge -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h5 class="fw-bold text-dark mb-0">Registration Details</h5>
                                @if($reg->status === 'confirmed')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Confirmed</span>
                                @elseif($reg->status === 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending Approval</span>
                                @endif
                            </div>

                            <!-- Registrant Info -->
                            <div class="row g-2 mb-4 text-dark" style="font-size: 0.9rem;">
                                <div class="col-5 text-muted">Name</div>
                                <div class="col-7 fw-semibold">{{ $reg->full_name ?? $reg->name }}</div>

                                <div class="col-5 text-muted">Email</div>
                                <div class="col-7">{{ $reg->email }}</div>

                                @if($reg->company_name)
                                <div class="col-5 text-muted">Company</div>
                                <div class="col-7">{{ $reg->company_name }}</div>
                                @endif

                                @if($reg->job_title)
                                <div class="col-5 text-muted">Job Title</div>
                                <div class="col-7">{{ $reg->job_title }}</div>
                                @endif

                                @if($event)
                                <div class="col-5 text-muted">Event</div>
                                <div class="col-7 fw-semibold">{{ $event->title }}</div>

                                @if($event->start_date)
                                <div class="col-5 text-muted">Date</div>
                                <div class="col-7">{{ $event->start_date->isoFormat('D MMMM YYYY') }}</div>
                                @endif

                                @if($event->location)
                                <div class="col-5 text-muted">Location</div>
                                <div class="col-7">{{ $event->location }}</div>
                                @endif
                                @endif

                                @if($reg->uuid)
                                <div class="col-5 text-muted">Registration ID</div>
                                <div class="col-7">
                                    <code class="text-muted" style="font-size: 0.75rem;">{{ $reg->uuid }}</code>
                                </div>
                                @endif
                            </div>

                            <!-- QR Code -->
                            @if($reg->uuid && $event)
                            <div class="text-center border-top pt-4">
                                <p class="small text-muted mb-2">Show this QR code at the venue for check-in</p>
                                <img src="{{ route('events.qr', [$event->slug, $reg->uuid]) }}"
                                    alt="QR Code - {{ $reg->uuid }}"
                                    class="img-fluid"
                                    style="width: 180px; height: 180px; image-rendering: pixelated;">
                            </div>
                            @endif

                            <!-- Status message -->
                            @if($reg->status === 'pending')
                            <div class="alert alert-warning rounded-3 mt-3 mb-0" style="font-size: 0.85rem;">
                                <strong>Pending Approval</strong> — Your registration is awaiting admin approval.
                                You will receive a confirmation email once approved.
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-2">
                        @if($event)
                            @php
                                $btnLabel = $event->success_button ?: 'Back to Event';
                                $btnUrl   = ($event->success_link_type === 'custom' && $event->success_link)
                                    ? $event->success_link
                                    : route('events.show', $event->slug);
                            @endphp
                            <a href="{{ $btnUrl }}"
                                class="btn btn-cta btn-warning text-white rounded-pill px-5 py-3 fw-bold shadow-lg"
                                style="font-size: 1.1rem;">{{ $btnLabel }}</a>
                        @else
                            <a href="{{ route('events.index') }}"
                                class="btn btn-cta btn-warning text-white rounded-pill px-5 py-3 fw-bold shadow-lg"
                                style="font-size: 1.1rem;">Back to Events</a>
                        @endif
                        <a href="{{ url('/') }}"
                            class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold"
                            style="font-size: 1.1rem;">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
