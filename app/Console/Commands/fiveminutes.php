<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ohlvc;
use Illuminate\Support\Str;
use App\Models\Symbol;

class fiveminutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '5min:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();
        $symbols = Symbol::all();
        foreach($symbols as $symbol){
            $request = $client->get('https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol='.$symbol->name.'&interval=5min&outputsize=full&apikey='.config('services.vantage.key'));
            $response = $request->getBody()->getContents();
            $json_response = json_decode($response, true);
            //$json_response["Meta Data"] #TODO include status w metadata
            foreach($json_response["Time Series (5min)"] as $date=>$ohlvc) {
                
                $ohlvc = Ohlvc::create([
                    'symbol_id' => $symbol->id,
                    'open' => $ohlvc['1. open'],
                    'close' => $ohlvc['4. close'],
                    'high'=> $ohlvc['2. high'],
                    'low'=> $ohlvc['3. low'],
                    'volume' => $ohlvc['5. volume'],
                    'slug' => Str::slug($date),
                    ]);
                
            }
        }   
    }
}
