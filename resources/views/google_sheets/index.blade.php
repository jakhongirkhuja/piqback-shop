<!DOCTYPE html>
<html>
<head>
    <title>Google Sheets Integration</title>
</head>
<body>
    <h1>Data from Google Sheets</h1>
    @if(isset($data))
        <ul>
        @foreach($data as $row)
            <li>{{ implode(', ', $row) }}</li>
        @endforeach
        </ul>
    @endif

    <hr>
    <h2>Update Google Sheets</h2>
    <form method="POST" action="/google-sheets">
        @csrf
        <input type="text" name="data[]" placeholder="Data 1">
        <input type="text" name="data[]" placeholder="Data 2">
        <input type="text" name="data[]" placeholder="Data 3">
        <button type="submit">Submit</button>
    </form>
</body>
</html>
