{{-- Flash + validation summary --}}
@php
    $flashSuccess = session('success');

    if (! $flashSuccess && session('status') === 'profile-updated') {
        $flashSuccess = 'Your profile has been updated.';
    } elseif (! $flashSuccess && session('status') === 'password-updated') {
        $flashSuccess = 'Your password has been updated.';
    } elseif (! $flashSuccess && session('status') === 'verification-link-sent') {
        $flashSuccess = 'A new verification link has been sent to your email.';
    } elseif (! $flashSuccess && is_string(session('status'))) {
        $flashSuccess = session('status');
    }

    $showFlash = $flashSuccess || $errors->any();
@endphp

@if ($showFlash)
    <div class="container flash-container">
        @if ($flashSuccess)
            <div class="alert alert-success" data-alert role="status">
                <span>{{ $flashSuccess }}</span>
                <button type="button" class="alert-dismiss" data-alert-dismiss aria-label="Dismiss">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error" data-alert role="alert">
                <div>
                    <strong class="alert-title">Please review the issues below</strong>
                    <ul class="alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="alert-dismiss" data-alert-dismiss aria-label="Dismiss">&times;</button>
            </div>
        @endif
    </div>
@endif
