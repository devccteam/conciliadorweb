<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'user_id',
        'layout_id',
        'status',
        'total_files',
        'error_message',
        'contract_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function layout()
    {
        return $this->belongsTo(Layout::class, 'layout_id');
    }

    public function sessions()
    {
        return $this->hasMany(ImportSession::class, 'import_id');
    }

    public function records()
    {
        return $this->hasManyThrough(ImportRecord::class, ImportSession::class, 'import_id', 'import_session_id');
    }
}
