<?php

namespace App\Http\Controllers\Htmx;

use App\Models\Article;
use App\Support\Helpers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditorStoreArticleRequest;

class HTMXEditorController extends Controller
{
    public function create()
    {
       
        return view('editor.partials.form');
    }

    public function store(EditorStoreArticleRequest $request)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        $validated = $request->safe()->all();
        if(env('COMPLETION')){
             // The message you want to send to OpenAI
    $message = $_POST['message'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://localhost:8080/v1/chat/completions',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
          "model": "gpt-3.5-turbo",
          "messages": [
            {
              "role": "user",
              "content": "'.$message.'"
            }
          ]
        }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$apiKey,
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    curl_close($curl);

    // Process the response from the OpenAI API
    $json = json_decode($response);
    $completion = $json->choices[0]->message->content;
    echo $response;
   echo $completion;

            $client = new \GuzzleHttp\Client();
            $message="write an article in english about ".$validated['content'].",use html tag to format";
            $url = 'http://localhost:8080/v1/chat/completions';
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer no-key',
            ];
            $body = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [['role' => 'user', 'content' => $message]],
            ];
            $response = $client->post($url, [
                'headers' => $headers, 
                'json' => $body,
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            $a_build = response()->json($result['choices'][0]['message']['content']);
            $content = $a_build;
        }else{$content=$validated['content'];}

        $article = Article::create([
            'user_id' => auth()->user()->id,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'body' => $content
        ]);

        // if ($validated['tags']) {

        //     $tags = json_decode($validated['tags']);
        //     $tagsArray = [];

        //     foreach ($tags as $key => $tag) {
        //         $tagsArray[] = $tag->value;
        //     }

        //     $article->attachTags($tagsArray);
        //}

        return response()->view('components.redirect', [
                'hx_get' => '/htmx/articles/' . $article->slug,
                'hx_target' => '#app-body',
                'hx_trigger' => 'load',
            ])
            ->withHeaders([
                'HX-Push-Url' => '/articles/' . $article->slug
            ]);
    }

    public function edit(Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }
        
        return view('editor.partials.form', ['article' => $article])
            .view('components.navbar', ['navbar_active' => ''])
            .view('components.htmx.head', [
                'page_title' => 'Editor —'
            ]);
    }

    public function update(EditorStoreArticleRequest $request, Article $article)
    {
        if (auth()->guest()) {
            return Helpers::redirectToSignIn();
        }

        $validated = $request->safe()->all();

        $article->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'body' => $validated['content']
        ]);

        if ($validated['tags']) {

            $tags = json_decode($validated['tags']);
            $tagsArray = [];

            foreach ($tags as $key => $tag) {
                $tagsArray[] = $tag->value;
            }

            $article->attachTags($tagsArray);
        }

        return response()->view('components.redirect', [
                'hx_get' => '/htmx/articles/' . $article->slug,
                'hx_target' => '#app-body',
                'hx_trigger' => 'load',
            ])
            ->withHeaders([
                'HX-Push-Url' => '/articles/' . $article->slug
            ]);
    }
}
