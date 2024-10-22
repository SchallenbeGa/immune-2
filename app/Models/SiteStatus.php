<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class SiteStatus extends Model
{
    protected $fillable = ['site_id', 'status', 'message','http_status'];
    protected $appends = ['formatted_created_at'];
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y.m.d');
    }
}
