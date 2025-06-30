<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Processing Authentication...</title>
</head>
<body>
<script>
    const data = {
        status: '{{ $status }}',
        @if(isset($token))
            token: '{{ $token }}',
            user: {!! $user !!}
        @else
            error: '{{ $error ?? "Unknown error." }}'
        @endif
    };

    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;secure;samesite=strict`;
    }

    function notifyParent(payload) {
        if (payload.status === 'success' && payload.token) {
            setCookie('auth_token', payload.token, 7);
        }

        if (window.opener) {
            window.opener.postMessage(payload, '*');
            window.close();
            return;
        }

        document.body.textContent = "Error: This window must be opened from the main application.";
    }

    notifyParent(data);
</script>
</body>
</html>
