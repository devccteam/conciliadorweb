<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'import_session_id',
        'num_doc',
        'date',
        'history',
        'debit_value',
        'credit_value',
        'interest',
        'fine',
        'discounts',
        'other_values',
        'client_supplier',
        'bank',
    ];

    public function importSession()
    {
        return $this->belongsTo(ImportSession::class, 'import_session_id');
    }
    
    public function import()
    {
        return $this->belongsTo(Import::class, 'import_id');
    }
}
