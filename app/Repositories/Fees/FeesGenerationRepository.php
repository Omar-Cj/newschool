<?php

namespace App\Repositories\Fees;

use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FeesGenerationRepository
{
    private $model;
    private $logModel;

    public function __construct(FeesGeneration $model, FeesGenerationLog $logModel)
    {
        $this->model = $model;
        $this->logModel = $logModel;
    }

    public function create(array $data): FeesGeneration
    {
        return $this->model->create($data);
    }

    public function update(FeesGeneration $generation, array $data): bool
    {
        return $generation->update($data);
    }

    public function show(int $id): ?FeesGeneration
    {
        return $this->model->with(['creator', 'logs.student'])->find($id);
    }

    public function findByBatchId(string $batchId): ?FeesGeneration
    {
        return $this->model->where('batch_id', $batchId)->first();
    }

    public function findWithLogs(int $id): ?FeesGeneration
    {
        return $this->model->with([
            'creator',
            'feesCollects',
            'logs' => function ($query) {
                // Eager load related student and feesCollect to avoid N+1 in views
                $query->with(['student', 'feesCollect'])->orderBy('created_at', 'desc');
            }
        ])->find($id);
    }

    public function getPaginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with([
                'creator',
                'feesCollects' => function ($query) {
                    $query->select('id', 'generation_batch_id', 'billing_period', 'date')
                          ->oldest()
                          ->limit(1);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getHistoryPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['creator'])
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->paginate($perPage);
    }

    public function getActiveGenerations(): Collection
    {
        return $this->model
            ->whereIn('status', ['pending', 'processing'])
            ->with(['creator'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getRecentGenerations(int $limit = 10): Collection
    {
        return $this->model
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getGenerationsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSuccessfulGenerations(): Collection
    {
        return $this->model
            ->where('status', 'completed')
            ->where('successful_students', '>', 0)
            ->with(['creator'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function getFailedGenerations(): Collection
    {
        return $this->model
            ->where('status', 'failed')
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createLog(array $data): FeesGenerationLog
    {
        return $this->logModel->create($data);
    }

    public function updateLog(FeesGenerationLog $log, array $data): bool
    {
        return $log->update($data);
    }

    public function getLogsByGeneration(int $generationId): Collection
    {
        return $this->logModel
            ->where('fees_generation_id', $generationId)
            ->with(['student', 'feesCollect'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLogsByStatus(int $generationId, string $status): Collection
    {
        return $this->logModel
            ->where('fees_generation_id', $generationId)
            ->where('status', $status)
            ->with(['student'])
            ->get();
    }

    public function getGenerationStats(int $generationId): array
    {
        $generation = $this->show($generationId);
        
        if (!$generation) {
            return [];
        }

        $logs = $this->getLogsByGeneration($generationId);
        
        return [
            'total_students' => $generation->total_students,
            'processed_students' => $generation->processed_students,
            'successful_students' => $generation->successful_students,
            'failed_students' => $generation->failed_students,
            'success_rate' => $generation->success_rate,
            'progress_percentage' => $generation->progress_percentage,
            'total_amount' => $generation->total_amount,
            'status' => $generation->status,
            'started_at' => $generation->started_at,
            'completed_at' => $generation->completed_at,
            'duration' => $generation->completed_at && $generation->started_at 
                ? $generation->started_at->diffInSeconds($generation->completed_at)
                : null,
            'failed_logs' => $logs->where('status', 'failed'),
            'skipped_logs' => $logs->where('status', 'skipped')
        ];
    }

    public function deleteGeneration(int $id): bool
    {
        $generation = $this->model->find($id);
        
        if (!$generation) {
            return false;
        }

        // Delete logs first due to foreign key constraint
        $this->logModel->where('fees_generation_id', $id)->delete();
        
        return $generation->delete();
    }

    public function cleanup(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $oldGenerations = $this->model
            ->where('status', 'completed')
            ->where('completed_at', '<', $cutoffDate)
            ->pluck('id');

        if ($oldGenerations->isEmpty()) {
            return 0;
        }

        // Delete logs first
        $this->logModel->whereIn('fees_generation_id', $oldGenerations)->delete();
        
        // Delete generations
        return $this->model->whereIn('id', $oldGenerations)->delete();
    }

    public function getSchoolStats(?int $schoolId = null): array
    {
        $query = $this->model->query();
        
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return [
            'total_generations' => $query->count(),
            'successful_generations' => $query->where('status', 'completed')->count(),
            'failed_generations' => $query->where('status', 'failed')->count(),
            'active_generations' => $query->whereIn('status', ['pending', 'processing'])->count(),
            'total_students_processed' => $query->sum('successful_students'),
            'total_amount_generated' => $query->where('status', 'completed')->sum('total_amount')
        ];
    }
}
