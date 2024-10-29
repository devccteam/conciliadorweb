<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Contract extends Authenticatable implements HasCurrentTenantLabel
{
    use HasFactory, Notifiable, HasUuids;

    protected static $infoUser;
    protected static $infoRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cpf_cnpj',
        'corporate_name',
        'street',
        'number',
        'neighborhood',
        'city',
        'complement',
        'state',
        'activity_branch',
        'name',
        'email',
        'contractor_type',
        'company_count',
        'user_count',
        'status',
        'linked_to_financial_system'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'contract_user');
    }
    public function company()
    {
        return $this->hasOne(Company::class, 'contract_id');
    }

    public function layout()
    {
        return $this->hasOne(Layout::class, 'contract_id');
    }

    public function layouts()
    {
        return $this->hasMany(Layout::class, 'contract_id');
    }

    public function getCurrentTenantLabel(): string
    {
        return 'Contrato Atual';
    }
}
