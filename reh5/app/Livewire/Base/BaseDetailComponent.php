<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

abstract class BaseDetailComponent extends Component
{
    /**
     * Model instance
     */
    protected $model;

    /**
     * Model ID
     */
    public $modelId;

    /**
     * Model data
     */
    public array $modelData = [];

    /**
     * Related data
     */
    public array $relatedData = [];

    /**
     * Loading state
     */
    public bool $isLoading = true;

    /**
     * Error state
     */
    public bool $hasError = false;

    /**
     * Error message
     */
    public string $errorMessage = '';

    /**
     * Initialize component
     */
    public function mount($id = null): void
    {
        $this->modelId = $id;
        $this->loadModel();
    }

    /**
     * Load model data
     */
    protected function loadModel(): void
    {
        try {
            $this->isLoading = true;
            $this->hasError = false;

            $this->model = $this->findModel($this->modelId);
            
            if (!$this->model) {
                $this->handleModelNotFound();
                return;
            }

            $this->modelData = $this->prepareModelData();
            $this->loadRelatedData();
            
        } catch (\Exception $e) {
            $this->handleError($e);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Find model by ID
     */
    abstract protected function findModel($id);

    /**
     * Prepare model data for display
     */
    protected function prepareModelData(): array
    {
        return $this->model->toArray();
    }

    /**
     * Load related data
     */
    protected function loadRelatedData(): void
    {
        // Override in child classes if needed
    }

    /**
     * Handle model not found
     */
    protected function handleModelNotFound(): void
    {
        $this->hasError = true;
        $this->errorMessage = 'Model not found';
        Log::warning('Model not found', [
            'component' => static::class,
            'model_id' => $this->modelId
        ]);
    }

    /**
     * Handle errors
     */
    protected function handleError(\Exception $e): void
    {
        $this->hasError = true;
        $this->errorMessage = 'An error occurred while loading the data';
        
        Log::error('Error loading model', [
            'component' => static::class,
            'model_id' => $this->modelId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Refresh model data
     */
    public function refresh(): void
    {
        $this->loadModel();
    }

    /**
     * Get model instance
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Check if model is loaded
     */
    public function isModelLoaded(): bool
    {
        return !$this->isLoading && !$this->hasError && $this->model !== null;
    }

    /**
     * Get model ID
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * Check if component has errors
     */
    public function hasErrors(): bool
    {
        return $this->hasError || !empty($this->errorMessage);
    }

    /**
     * Clear errors
     */
    public function clearErrors(): void
    {
        $this->hasError = false;
        $this->errorMessage = '';
    }

    /**
     * Get related data
     */
    public function getRelatedData(): array
    {
        return $this->relatedData;
    }

    /**
     * Set related data
     */
    public function setRelatedData(array $data): void
    {
        $this->relatedData = $data;
    }

    /**
     * Add related data
     */
    public function addRelatedData(string $key, $data): void
    {
        $this->relatedData[$key] = $data;
    }

    /**
     * Remove related data
     */
    public function removeRelatedData(string $key): void
    {
        unset($this->relatedData[$key]);
    }
}
