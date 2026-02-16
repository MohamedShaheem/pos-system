<?php
// Updated CustomerAdvanceDetail Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAdvanceDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_advance_id',
        'amount',
        'note'
    ];

    public function customerAdvance()
    {
        return $this->belongsTo(CustomerAdvance::class, 'customer_advance_id');
    }

    // In CustomerAdvanceDetail.php
    protected $casts = [
        'amount' => 'float'
    ];

}