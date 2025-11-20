{{-- Subscription Grace Period Warning Banner --}}
@if(isset($subscription_warning) && $subscription_warning)
<div class="alert alert-warning alert-dismissible fade show subscription-grace-warning" role="alert" style="margin: 20px; border-left: 5px solid #ff9800; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-right: 15px; color: #ff9800;"></i>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-1">
                <strong>⚠️ Subscription Expiry Warning</strong>
            </h5>
            <p class="mb-0">
                {{ $subscription_warning }}
            </p>
            <p class="mb-0 mt-2">
                <small>
                    <strong>Contact Telesom Sales:</strong> Email: sales@telesom.net | Phone: +252 61 5555555
                </small>
            </p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<style>
.subscription-grace-warning {
    animation: pulse-warning 2s ease-in-out infinite;
    position: sticky;
    top: 70px;
    z-index: 1000;
}

@keyframes pulse-warning {
    0%, 100% {
        background-color: #fff3cd;
    }
    50% {
        background-color: #ffe4b3;
    }
}
</style>
@endif
