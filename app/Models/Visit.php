<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'url',
        'http_method',
        'user_agent',
        'referer',
        'country',
    ];
    public function getBrowserAttribute()
    {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);
        return $agent->browser();
    }

    public function getOsAttribute()
    {
        $agent = new Agent();
        $agent->setUserAgent($this->user_agent);
        return $agent->platform();
    }
}
