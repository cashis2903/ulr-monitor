<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LinkSample;


class Link extends Model
{
    protected $fillable = ['url'];

    public function samples()
    {
        return $this->hasMany(LinkSample::class, 'link_id', 'id');
    }
}
