<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\View;

class NewsController extends Controller
{
    public function fetchNews()
    {
        set_time_limit(300);
        $result = shell_exec("C:\Users\Denica\PhpstormProjects\iisok_project\\venv\Scripts\python.exe C:\Users\Denica\PhpstormProjects\iisok_project\python\\fetch_news.py");


        $data = json_decode($result, true);

        // Save articles to the database
        if (is_array($data)) {
            foreach ($data as $article) {
                NewsArticle::updateOrCreate(
                    ['url' => $article['url']], // Ensure uniqueness
                    [
                        'title' => $article['title'],
                        'content' => $article['content'],
                        'source' => $article['source']
                    ]
                );
            }
        }

        $tempFilePath = 'C:\\Users\\Denica\\PhpstormProjects\\iisok_project\\storage\\app\\private\\temp_articles.json';
        Storage::disk('local')->put('temp_articles.json', $result);

        $filePath = escapeshellarg($tempFilePath);
        // Construct the shell command with arguments
        $comparison = escapeshellcmd("C:\Users\Denica\PhpstormProjects\iisok_project\\venv\Scripts\python.exe C:\Users\Denica\PhpstormProjects\iisok_project\python\\compare.py $filePath");
        $output = shell_exec($comparison);

        //Execute the shell command
        $comparisonResults = json_decode($output, true);
        Storage::delete('temp_articles.json');


        // Check if the comparisonResults are valid
        if ($comparisonResults) {
            // Save comparison results to the database
            foreach ($comparisonResults as $result) {
                $article1 = NewsArticle::firstOrCreate(['title' => $result['news1_title'], 'content' => $result['news1_content']]);
                $article2 = NewsArticle::firstOrCreate(['title' => $result['news2_title'], 'content' => $result['news2_content']]);

                Comparison::updateOrCreate(
                    [
                        'article1_id' => $article1->id,
                        'article2_id' => $article2->id
                    ],
                    ['similarity' => $result['similarity_original']]
                );
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process comparison results.',
            ], 500);
        }

        // Return the comparison results to the view
        return view('news', [
            'fetchedNews' => $data,
            'news' => $comparisonResults
        ]);
    }

    public function showDetails($id)
    {
        set_time_limit(300);
        // Find the article by ID
        $article = NewsArticle::findOrFail($id);

        // Check if sentiment analysis has already been done
        if ($article->sentiment==null or $article->sentiment=='null') {
            // Perform sentiment analysis on the selected article

            $sentimentResult = escapeshellcmd("C:\\Users\\Denica\\PhpstormProjects\\iisok_project\\venv\\Scripts\\python.exe C:\\Users\\Denica\\PhpstormProjects\\iisok_project\\python\\sentiment_analysis.py $article->content");
            $output = shell_exec($sentimentResult);

            // Decode the JSON result from Python (assuming the sentiment script returns JSON)
            $sentiment = json_decode($output, true);
            if($sentiment==null or $sentiment == 'null'){
                return response()->json([
                    'error' => 'Custom error message',
                ], 400);
            }
            // Save the sentiment analysis to the database
            $article->sentiment = json_encode($sentiment);
            $article->save();
        } else {
            // Sentiment already exists, retrieve from the database
            $sentiment = json_decode($article->sentiment, true);
        }

        // Retrieve the stored similarities for this article
        $similarArticles = $this->getTopSimilarArticles($article);

        // Pass data to the view to show the sentiment and comparison results
        return view('details', [
            'article' => $article,
            'sentiment' => $sentiment,
            'similarArticles' => $similarArticles,
        ]);
    }


    protected function getTopSimilarArticles($article)
    {
        // Fetch all articles except the current one
        $allArticles = NewsArticle::where('id', '!=', $article->id)->get();
        $similarArticles = [];

        foreach ($allArticles as $otherArticle) {
            // Check if the comparison already exists in the database
            $comparison = Comparison::where(function ($query) use ($article, $otherArticle) {
                $query->where('article1_id', $article->id)
                    ->where('article2_id', $otherArticle->id);
            })->orWhere(function ($query) use ($article, $otherArticle) {
                $query->where('article1_id', $otherArticle->id)
                    ->where('article2_id', $article->id);
            })->first();

            // If the comparison doesn't exist, do the comparison
            if (!$comparison) {
                $similarity = $this->compareArticles($article->content, $otherArticle->content);
                if($similarity==0.0){
                    return response()->json([
                        'error' => 'Custom error message',
                    ], 400);
                }
                // Save the new comparison to the database
                $comparison = Comparison::create([
                    'article1_id' => $article->id,
                    'article2_id' => $otherArticle->id,
                    'similarity' => $similarity,
                ]);
            }

            // Add the article and similarity to the results array
            $similarArticles[] = [
                'article' => $otherArticle,
                'similarity' => $comparison->similarity
            ];
        }

        // Sort by similarity and return the top 10%
        usort($similarArticles, function ($a, $b) {
            return $b['similarity'] - $a['similarity'];
        });

        return array_slice($similarArticles, 0, ceil(count($similarArticles) * 0.10));
    }

    // Function to dynamically compare articles using Python
    protected function compareArticles($content1, $content2)
    {
        // Save articles' content to a temporary file for Python script
        $tempData = json_encode(['article1' => $content1, 'article2' => $content2]);
        $tempFilePath = 'C:\\Users\\Denica\\PhpstormProjects\\iisok_project\\storage\\app\\private\\temp_compare.json';
        Storage::disk('local')->put('temp_compare.json', $tempData);

        $filePath = escapeshellarg($tempFilePath);
        // Construct the shell command with arguments
        $comparison = escapeshellcmd("C:\Users\Denica\PhpstormProjects\iisok_project\\venv\Scripts\python.exe C:\Users\Denica\PhpstormProjects\iisok_project\python\\compare_two_articles.py $filePath");
        $output = shell_exec($comparison);

        //Execute the shell command
        $comparisonResults = json_decode($output, true);

        Storage::delete($tempFilePath);

        return $comparisonResults['similarity_original'] ?? 0;
    }

    public function showComparisonForm()
    {
        // Fetch all saved articles from the database
        $articles = NewsArticle::all();

        return view('compare', compact('articles'));
    }

    public function compareSavedArticles(Request $request)
    {
        $article1 = NewsArticle::findOrFail($request->article1_id);
        $article2 = NewsArticle::findOrFail($request->article2_id);

        // Call the function to calculate similarity (could be from a Python script or Laravel function

        $comparison = Comparison::where(function ($query) use ($article1, $article2) {
            $query->where('article1_id', $article1->id)
                ->where('article2_id', $article2->id);
        })->orWhere(function ($query) use ($article1, $article2) {
            $query->where('article1_id', $article2->id)
                ->where('article2_id', $article1->id);
        })->first();

        // If the comparison doesn't exist, do the comparison
        if (!$comparison) {
            $similarity = $this->compareArticles($article1->content, $article2->content);
            if($similarity==0.0){
                return response()->json([
                    'error' => 'Custom error message',
                ], 400);
            }
            // Save the new comparison to the database
            $comparison = Comparison::create([
                'article1_id' => $article1->id,
                'article2_id' => $article2->id,
                'similarity' => $similarity,
            ]);
        }

        // Return the view with the similarity result
        return view('comparison_result', [
            'article1' => $article1,
            'article2' => $article2,
            'similarity' => $comparison->similarity
        ]);
    }

    public function compareTexts(Request $request)
    {
        $text1 = $request->text1;
        $text2 = $request->text2;

        // Call the function to calculate similarity between the two texts (via Python script or internal function)
        $similarity = $this->compareArticles($text1, $text2);

        // Return the view with the similarity result
        return view('comparison_result', [
            'text1' => $text1,
            'text2' => $text2,
            'similarity' => $similarity
        ]);
    }

    public function fetchNews2(): \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
    {
        set_time_limit(300);
        // Define the Python script path
        $pythonScriptPath = base_path('C:\Users\Denica\PhpstormProjects\iisok_project\python\fetch_news_and_compare.py');
        $env = array_merge($_ENV, [
            'PYTHONASYNCIODEBUG' => '1'
        ]);

        // Use the Python executable inside the virtual environment
        //$pythonExec = base_path('C:\Users\Denica\PhpstormProjects\iisok_project\venv\Scripts\python.exe'); // Correct path to Python in the virtual environment
        $result = shell_exec("C:\Users\Denica\PhpstormProjects\iisok_project\\venv\Scripts\python.exe C:\Users\Denica\PhpstormProjects\iisok_project\python\\fetch_news_and_compare.py");
        // Create the process with the full command (use shell)
        //$process = new Process([$pythonExec, $pythonScriptPath], null, null, null, null);

        // Run the process (without shell argument now)
        //$process->run();

        // Check if the process is successful
        //if (!$process->isSuccessful()) {
        //    // If there's an error, return the error message
        //    return response()->json([
        //        'error' => $process->getErrorOutput(),
        //        'output' => $process->getOutput()
        //    ]);
        //}
        // Get the output of the Python script
        $data = json_decode($result, true);

        if (isset($data['fetched_news'])) {
            $news = $data['comparison_results']; // Use comparison results for display
            $fetchedNews = $data['fetched_news']; // Use fetched news for storing

            return view('news', ['news' => $news, 'fetchedNews' => $fetchedNews]);
        } else {
            return response()->json(['error' => 'Failed to fetch news'], 500);
        }
    }

    public function store(Request $request)
    {
        $articles = json_decode($request->input('articles'), true);

        // Ensure $articles is an array
        if (is_array($articles)) {
            foreach ($articles as $article) {
                NewsArticle::updateOrCreate([
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'source' => $article['source'],
                    'url' => $article['url'],
                ]);
            }

            return redirect()->route('index')->with('success', 'Articles stored successfully!');
        } else {
            return redirect()->back()->with('error', 'Invalid data format. Articles must be an array.');
        }
    }

    // Fetch the latest articles from the database
    public function index()
    {
        $storedArticles = NewsArticle::all();
        return view('index',['storedArticles' => $storedArticles]);
    }
}

