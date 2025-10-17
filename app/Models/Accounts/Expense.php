<?php

namespace App\Models\Accounts;

use App\Models\BaseModel;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'session_id',
        'name',
        'expense_category_id',
        'expense_head',
        'date',
        'invoice_number',
        'amount',
        'upload_id',
        'description',
        'branch_id',
    ];

    /**
     * Get the upload document for the expense.
     *
     * @return BelongsTo
     */
    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'upload_id', 'id');
    }

    /**
     * Get the expense category for the expense.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    }

    /**
     * Get the account head for the expense (deprecated - for backward compatibility).
     *
     * @return BelongsTo
     * @deprecated Use category() relationship instead
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(AccountHead::class, 'expense_head', 'id');
    }
}
