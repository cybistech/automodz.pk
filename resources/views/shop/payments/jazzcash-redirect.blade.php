<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to JazzCash...</title>
    <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0f172a;color:#fff}</style>
</head>
<body>
    <div style="text-align:center">
        <p>Redirecting to JazzCash...</p>
        <p style="color:#94a3b8;font-size:14px">Please wait, do not close this window.</p>
    </div>
    <form id="jazzcash-form" action="{{ $endpoint }}" method="POST">
        @foreach($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>document.getElementById('jazzcash-form').submit();</script>
</body>
</html>
