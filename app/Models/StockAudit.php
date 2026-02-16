<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockAudit extends Model
{
    protected $fillable = [
        'audit_reference',
        'product_category_id',
        'created_by',
        'status',
        'expected_count',
        'scanned_count',
        'notes',
        'started_at',
        'completed_at',
        'audit_type' // NEW: 'category' or 'all'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
       
        static::creating(function ($audit) {
            if (empty($audit->audit_reference)) {
                $audit->audit_reference = 'AUD-' . date('Y') . '-' . str_pad(
                    self::whereYear('created_at', date('Y'))->count() + 1,
                    3, '0', STR_PAD_LEFT
                );
            }
            
            // Set default audit type if not specified
            if (empty($audit->audit_type)) {
                $audit->audit_type = $audit->product_category_id ? 'category' : 'all';
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockAuditItem::class);
    }

    public function getMissingProducts()
    {
        $scannedProductNos = $this->items()->pluck('product_no')->toArray();
    
        $query = Product::where('qty', '>', 0)
            ->where('is_approved', 1)
            ->whereIn('product_type', ['gold', 'silver'])
            ->where('status', 'active');
        
        // If category audit, filter by category
        if ($this->audit_type === 'category' && $this->product_category_id) {
            $query->where('product_category_id', $this->product_category_id);
        }
        // If 'all' audit, get all products
        
        return $query->whereNotIn('product_no', $scannedProductNos)->get();
    }

    public function getExtraProducts()
    {
        $scannedProductNos = $this->items()->pluck('product_no')->toArray();
        
        $validQuery = Product::query();
        
        // If category audit, only products from that category are valid
        if ($this->audit_type === 'category' && $this->product_category_id) {
            $validQuery->where('product_category_id', $this->product_category_id);
        }
        // If 'all' audit, all products are valid
        
        $validProductNos = $validQuery->pluck('product_no')->toArray();
       
        return $this->items()
            ->whereNotIn('product_no', $validProductNos)
            ->get();
    }

    public function isAllProductsAudit()
    {
        return $this->audit_type === 'all';
    }

    public function isCategoryAudit()
    {
        return $this->audit_type === 'category';
    }
}