<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'category_id',
        'bpom_code',
        'stock',
        'is_by_order',
        'price',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Assign category from BPOM reference data based on partial name match.
     *
     * @return void
     */
    public function assignCategoryFromBpom()
    {
        $bpomRecord = DB::table('bpom_reference_data')
            ->where('name', 'like', '%' . $this->name . '%')
            ->first();

        if ($bpomRecord) {
            $category = DB::table('categories')
                ->where('name', $bpomRecord->category)
                ->first();

            if ($category) {
                $this->category_id = $category->id;
                $this->save();
            }
        }
    }
}
