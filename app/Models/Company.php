<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'cpf_cnpj',
        'corporate_name',
        'street',
        'number',
        'neighborhood',
        'city',
        'complement',
        'state',
        'activity_branch',
        'has_observations',
        'require_justification',
        'has_checked_field',
        'tax_regime',
        'layout_reason_id',
        'layout_financial_id',
        'require_approver',
        'approver_user_id',
        'approx_value_enabled',
        'approx_value_percentage',
        'has_fixed_account',
        'active_interest_account',
        'passive_interest_account',
        'discounts_obtained_account',
        'discounts_given_account',
        'require_documents',
        'contract_id',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function layouts()
    {
        return $this->belongsToMany(Layout::class, 'company_layout', 'company_id', 'layout_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
