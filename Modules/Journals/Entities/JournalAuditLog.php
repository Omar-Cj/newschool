<?php

namespace Modules\Journals\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class JournalAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id',
        'action',
        'performed_by',
        'performed_at',
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'action' => 'string',
    ];

    /**
     * Get the journal that this audit log belongs to
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the user who performed the action
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope to get only open actions
     */
    public function scopeOpened($query)
    {
        return $query->where('action', 'opened');
    }

    /**
     * Scope to get only close actions
     */
    public function scopeClosed($query)
    {
        return $query->where('action', 'closed');
    }

    /**
     * Scope to order by most recent first
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('performed_at', 'desc');
    }

    /**
     * Get formatted action display
     */
    public function getActionDisplayAttribute(): string
    {
        return ucfirst($this->action);
    }

    protected static function newFactory()
    {
        return \Modules\Journals\Database\factories\JournalAuditLogFactory::new();
    }
}
