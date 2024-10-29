<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'format',
        'sector',
        'movement_type',
        'start_row',
        'num_doc_column',
        'parcel_separator',
        'date_column',
        'history_column',
        'history_2_lines_column',
        'debit_value_column',
        'credit_value_column',
        'interest_column',
        'fine_column',
        'discounts_column',
        'other_values_column',
        'ignore_history',
        'client_supplier_column',
        'debit_credit_column',
        'bank_column',
        'consider_previous_date',
        'consider_previous_client_supplier',
        'consider_previous_history',
        'consider_previous_bank',
        'is_default_layout',
        'contract_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_layout');
    }
}
