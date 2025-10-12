<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ReportParameter Model
 *
 * Defines dynamic parameters for reports. Each parameter can be a text input, dropdown,
 * date picker, etc. Parameters can depend on parent parameters for cascading dropdowns.
 *
 * @property int $id
 * @property int $report_id
 * @property string $name
 * @property string $label
 * @property string $type
 * @property string|null $placeholder
 * @property string|null $value_type
 * @property string|null $values
 * @property string|null $default_value
 * @property int|null $parent_id
 * @property int $is_required
 * @property int $display_order
 * @property array|null $validation_rules
 *
 * @property-read ReportCenter $report
 * @property-read ReportParameter|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<ReportParameter> $children
 */
class ReportParameter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_parameters';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'report_id',
        'name',
        'label',
        'type',
        'placeholder',
        'value_type',
        'values',
        'default_value',
        'parent_id',
        'is_required',
        'display_order',
        'validation_rules',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'report_id' => 'integer',
        'parent_id' => 'integer',
        'is_required' => 'integer',
        'display_order' => 'integer',
        'validation_rules' => 'array',
    ];

    /**
     * Valid parameter types.
     *
     * @var array<string>
     */
    public const TYPES = [
        'text',
        'number',
        'date',
        'datetime',
        'select',
        'multiselect',
        'checkbox',
        'radio',
    ];

    /**
     * Get the report that owns this parameter.
     *
     * @return BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(ReportCenter::class, 'report_id', 'id');
    }

    /**
     * Get the parent parameter (for dependent parameters).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ReportParameter::class, 'parent_id', 'id');
    }

    /**
     * Get child parameters that depend on this parameter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(ReportParameter::class, 'parent_id', 'id');
    }

    /**
     * Scope to order parameters by display_order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope to get required parameters only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', 1);
    }

    /**
     * Scope to get optional parameters only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOptional($query)
    {
        return $query->where('is_required', 0);
    }

    /**
     * Check if this parameter has a parent.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this parameter is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->is_required === 1;
    }

    /**
     * Check if this parameter is a dropdown (select/multiselect).
     *
     * @return bool
     */
    public function isDropdown(): bool
    {
        return in_array($this->type, ['select', 'multiselect'], true);
    }

    /**
     * Check if values are static (JSON array) or dynamic (query).
     *
     * @return bool
     */
    public function hasStaticValues(): bool
    {
        if (empty($this->values)) {
            return false;
        }

        $decoded = json_decode($this->values, true);

        if (!is_array($decoded)) {
            return false;
        }

        // Check if it's a static array or contains query
        return !isset($decoded['source']) && !isset($decoded['query']);
    }

    /**
     * Check if values come from a dynamic query.
     *
     * @return bool
     */
    public function hasDynamicQuery(): bool
    {
        if (empty($this->values)) {
            return false;
        }

        $decoded = json_decode($this->values, true);

        return is_array($decoded) && (isset($decoded['query']) || isset($decoded['source']));
    }

    /**
     * Get parsed values (for static values).
     *
     * @return array
     */
    public function getParsedValues(): array
    {
        if (empty($this->values)) {
            return [];
        }

        $decoded = json_decode($this->values, true);

        if (!is_array($decoded)) {
            return [];
        }

        // If it's a static array of values
        if (!isset($decoded['source']) && !isset($decoded['query'])) {
            return $decoded;
        }

        return [];
    }

    /**
     * Get the SQL query for dynamic values.
     *
     * @return string|null
     */
    public function getQueryString(): ?string
    {
        if (empty($this->values)) {
            return null;
        }

        $decoded = json_decode($this->values, true);

        if (is_array($decoded) && isset($decoded['query'])) {
            return $decoded['query'];
        }

        return null;
    }

    /**
     * Get validation rules as array.
     *
     * @return array
     */
    public function getValidationRulesArray(): array
    {
        if (empty($this->validation_rules)) {
            $rules = [];

            if ($this->isRequired()) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            // Add type-specific rules
            switch ($this->type) {
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'date':
                    $rules[] = 'date';
                    break;
                case 'datetime':
                    $rules[] = 'date_format:Y-m-d H:i:s';
                    break;
                case 'select':
                case 'radio':
                    $rules[] = 'string';
                    break;
                case 'multiselect':
                    $rules[] = 'array';
                    break;
            }

            return $rules;
        }

        return is_array($this->validation_rules) ? $this->validation_rules : [];
    }
}
