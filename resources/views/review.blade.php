<!-- resources/views/review.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Prediction</title>
</head>
<body>
    <h1>Review Sentiment Prediction</h1>

    <form action="{{ route('predict-review') }}" method="POST">
        @csrf
        <label for="review">Enter a review:</label><br><br>
        <textarea name="review" id="review" rows="4" cols="50"></textarea><br><br>
        <button type="submit">Predict Sentiment</button>
    </form>

    @if(session('predicted_sentiment'))
        <p>Predicted Sentiment: {{ session('predicted_sentiment') }}</p>
    @endif
</body>
</html>
