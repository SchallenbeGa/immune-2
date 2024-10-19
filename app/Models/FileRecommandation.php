<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRecommandation extends Model
{
    protected $fillable = ['project_file_id','file_path', 'action_performed', 'recommendation', 'created_at'];
}