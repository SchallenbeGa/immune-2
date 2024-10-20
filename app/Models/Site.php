<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['url', 'name', 'status','screenshot_path'];

    public function statuses()
    {
        return $this->hasMany(SiteStatus::class);
    }
}
