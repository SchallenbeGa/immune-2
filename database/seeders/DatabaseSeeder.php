<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tag;
use App\Models\User;
use App\Models\Article;
use App\Models\Site;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->create([
            'email' => 'test@email.com',
            'role' => 3,
            'password' => 'toor'
        ]);
        Site::factory()->create([
            'name' => 'netbanking',
            'url' => 'https://netbanking.bcn.ch/authen/login?execution=e1s1',
        ]);
        Site::factory()->create([
            'name' => 'netbanking-a',
            'url' => 'https://netbanking-a.bcn.ch/authen/login?execution=e1s1',
        ]);
        Article::factory()->create([
            'user_id'=>1,
            'title' => 'automat trading (binance api)',
            'description' => 'simple task : buy and sell with rules',
            'body' => '<h1>Buy and Sell automat in Python</h1><p>After implementing the basic buy and sell call to the api, the hardest past is defining the rule /analysis needed.</p><p>I have found that some financial product are way easier to automat, by example the future trading is made in a way that we can "secure" some margin, negative side is that future contract force the dev to monitor the delay between two orders.</p><p>Developped to be a SAAS, not crazy enough to sell it.</p><a target="_blank" href="https://lrz.gabriel0.com">example</a>'
        ]);
        Article::factory()->create([
            'user_id'=>1,
            'title' => 'simple inventory for material monitoring',
            'description' => 'simple task : create an inventory based on material and users (employees)',
            'body' => '<h1>Inventory in a sme</h1><p>Nothing much than a crud with a 1-1 relation...</p><p>When calling the material manager, the employee usually have to give the reference of the material, here the way was with a QR Code...</p><a target="_blank" href="https://inventory.gabriel0.com">example</a>'
        ]);
        Article::factory()->create([
            'user_id'=>1,
            'title' => 'webapp monitoring',
            'description' => 'simple task : create an effective way to get a webapp status',
            'body' => '<h1>Status page</h1><p>Basic curl are most of the time enough, add some fsockopen for tcp and udp.</p><p>In a time of SLA and cloud, most of the clients have no monitoring solution.</p><a target="_blank" href="https://status.gabriel0.com">example</a>'
        ]);

        // $users = User::factory()->count(20)->create();

        // foreach ($users as $user) {
        //     $user->followers()->attach($users->random(rand(0, 5)));
        // }

        // $articles = Article::factory()
        //     ->count(30)
        //     ->state(new Sequence(fn() => [
        //         'user_id' => $users->random(),
        //     ]))
        //     ->create();

        // $tags = Tag::factory()->count(20)->create();

        // foreach ($articles as $article) {
        //     $article->tags()->attach($tags->random(rand(0, 6)));
        //     $article->favoritedUsers()->attach($users->random(rand(0, 8)));
        // }

        // Comment::factory()
        //     ->count(60)
        //     ->state(new Sequence(fn() => [
        //         'article_id' => $articles->random(),
        //         'user_id' => $users->random(),
        //     ]))
        //     ->create();
    }
}
