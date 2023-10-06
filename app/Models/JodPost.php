<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JodPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'work_period',
        'experience',
        'hourly_rate',
        'attach_file',
    ];

    public function job_skill()
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id');
    }
 /*    public function jobSkills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id')->pluck('name');
    } */
}
