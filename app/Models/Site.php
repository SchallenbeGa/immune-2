<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;
    protected $fillable = ['url', 'name', 'status','screenshot_path','response','type','port','header','method'];

    public function statuses()
    {
        return $this->hasMany(SiteStatus::class);
    }
}
