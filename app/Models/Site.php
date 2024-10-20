<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['url', 'name', 'status'];

    public function statuses()
    {
        return $this->hasMany(SiteStatus::class);
    }
}
