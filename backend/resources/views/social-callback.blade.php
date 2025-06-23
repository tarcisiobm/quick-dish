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

    function notifyParent(payload) {
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
