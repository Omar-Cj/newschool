@extends('errors.master')

@section('title', 'Feature Access Denied')
@section('main')
<main>
    <section class="error-wrapper p-0 m-0 text-center d-flex justify-content-center align-items-center flex-column">
        <div class="error-content p-0 m-0 text-center d-flex justify-content-center align-items-center flex-column">
            <!-- error image -->
            <img src="{{asset('backend')}}/assets/images/error/error500.png" alt="Access Denied" />

            <!-- Head text -->
            <h1 class="mt-30">Feature Not Available</h1>

            <!-- Error text -->
            <p class="mt-10">
                {{ $message ?? 'This feature is not available in your current package.' }}
            </p>

            @if(isset($currentPackage))
                <div class="alert alert-info mt-20" style="max-width: 500px;">
                    <strong>Current Package:</strong> {{ $currentPackage }}
                </div>
            @endif

            <!-- Action buttons -->
            <div class="btn-back-to-homepage mt-28">
                @if(isset($upgradeUrl))
                    <a href="{{ $upgradeUrl }}" class="submit-button pv-16 btn ot-btn-primary mr-2">
                        <i class="fas fa-arrow-up"></i> Upgrade Package
                    </a>
                @endif

                <a href="{{url('dashboard')}}" class="submit-button pv-16 btn ot-btn-primary">
                    Back to Dashboard
                </a>
            </div>

            @if(isset($contactUrl))
                <div class="mt-20">
                    <p>Need help? <a href="{{ $contactUrl }}">Contact Support</a></p>
                </div>
            @endif

            @if(config('app.debug') && isset($featureAttribute))
                <div class="alert alert-secondary mt-20" style="max-width: 600px;">
                    <small><strong>Debug:</strong> Feature required: <code>{{ $featureAttribute }}</code></small>
                </div>
            @endif
        </div>
    </section>
</main>
@endsection
