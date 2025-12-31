<!DOCTYPE html>
<html>

<head>
    <title>ANWB Electriciteits tarieven voor {{ $results['electra']['date'] }}</title>
</head>

<body style="font-family: Corbel; color: #888;">
    <h1>ANWB Electriciteits tarieven voor {{ $results['electra']['date'] }}</h1>

    <p>Gemiddeld eletra: {{ number_format($results['electra']['average'], 2) }}</p>
    <p>Gemiddeld gas: {{ number_format($results['gas']['average'], 2) }}</p>


</body>

</html>
