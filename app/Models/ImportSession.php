<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'import_id',
        'file_name',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function import()
    {
        return $this->belongsTo(Import::class, 'import_id');
    }

    public function records()
    {
        return $this->hasMany(ImportRecord::class, 'import_session_id');
    }
}
