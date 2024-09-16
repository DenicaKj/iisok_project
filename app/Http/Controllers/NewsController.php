<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\View;

class NewsController extends Controller
{
    public function fetchNews(): \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
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
                NewsArticle::create([
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

