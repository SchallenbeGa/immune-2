<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteStatus extends Model
{
    protected $fillable = ['site_id', 'status', 'message'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
