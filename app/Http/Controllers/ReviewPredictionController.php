<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rubix\ML\Classifiers\LogisticRegression;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Transformers\WordCountVectorizer;
use Rubix\ML\Pipeline;

class ReviewPredictionController extends Controller
{
    protected $pipeline;
    protected $trainDataset;

    public function __construct()
    {
        // Initialize the classifier and transformers within a pipeline
        $this->pipeline = new Pipeline([
            new TextNormalizer(),
            new WordCountVectorizer(1000), // Limit vocabulary size to 1000 words
        ], new LogisticRegression()); // Use Logistic Regression

        // Prepare training samples and labels
        $samples = [
            'I love this product',
            'This is amazing!',
            'Terrible experience',
            'I hate this',
            'Excellent quality and fast delivery',
            'Worst purchase I have ever made',
            'Highly recommend this to everyone',
            'Not worth the money',
            'I am extremely happy with this',
            'Very disappointed with the service',
            'Fantastic, exceeded my expectations',
            'Product broke after one use',
            'The color and design are beautiful',
            'Completely useless and waste of time',
            'Absolutely satisfied with my purchase',
            'I will never buy from here again',
            'The customer support was very helpful',
            'Horrible packaging, arrived damaged',
            'Really great experience overall',
            'Feels cheap and poorly made',
        ];

        $labels = [
            'positive', 'positive', 'negative', 'negative', 'positive',
            'negative', 'positive', 'negative', 'positive', 'negative',
            'positive', 'negative', 'positive', 'negative', 'positive',
            'negative', 'positive', 'negative', 'positive', 'negative',
        ];

        // Train the model
        $this->trainDataset = new Labeled($samples, $labels);
        $this->pipeline->train($this->trainDataset);
    }

    public function trainAndPredict(Request $request)
    {
        // Get and preprocess the review
        $review = $request->input('review');

        // Wrap the input review in an Unlabeled dataset
        $dataset = new Unlabeled([$review]);

        // Make a prediction
        $prediction = $this->pipeline->predict($dataset);

        return response()->json(['prediction' => $prediction[0]]);
    }

    public function checkAccuracy()
    {
        // Testing data (these should not overlap with the training data)
        $testSamples = [
            'I really love this!',
            'This was a bad experience',
            'I am so happy with this',
            'Not what I expected',
            'Great product overall',
            'Will not buy again',
            'Absolutely perfect!',
            'Very poor service',
        ];

        $testLabels = [
            'positive', 'negative', 'positive', 'negative', 'positive',
            'negative', 'positive', 'negative',
        ];

        // Create a labeled dataset for testing
        $testDataset = new Labeled($testSamples, $testLabels);

        // Predict the labels for the test dataset
        $predictions = $this->pipeline->predict($testDataset);

        // Calculate accuracy
        $correct = 0;
        $total = count($testLabels);

        for ($i = 0; $i < $total; $i++) {
            if ($predictions[$i] === $testLabels[$i]) {
                $correct++;
            }
        }

        $accuracy = ($correct / $total) * 100; // Convert to percentage

        return response()->json(['accuracy' => $accuracy . '%']);
    }
}
