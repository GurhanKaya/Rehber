<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

abstract class BaseEditComponent extends BaseFormComponent
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
     * Original model data
     */
    protected array $originalData = [];

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

            $this->formData = $this->prepareFormData();
            $this->originalData = $this->formData;
            
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
     * Prepare form data from model
     */
    protected function prepareFormData(): array
    {
        return $this->model->toArray();
    }

    /**
     * Handle model not found
     */
    protected function handleModelNotFound(): void
    {
        $this->hasError = true;
        $this->errorMessage = 'Model not found';
        Log::warning('Model not found for editing', [
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
        
        Log::error('Error loading model for editing', [
            'component' => static::class,
            'model_id' => $this->modelId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Check if form has changes
     */
    public function hasChanges(): bool
    {
        return $this->formData !== $this->originalData;
    }

    /**
     * Reset form to original data
     */
    public function resetToOriginal(): void
    {
        $this->formData = $this->originalData;
        $this->resetErrorBag();
    }

    /**
     * Get original data
     */
    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    /**
     * Get changed fields
     */
    public function getChangedFields(): array
    {
        $changed = [];
        
        foreach ($this->formData as $key => $value) {
            if (isset($this->originalData[$key]) && $this->originalData[$key] !== $value) {
                $changed[$key] = [
                    'old' => $this->originalData[$key],
                    'new' => $value
                ];
            }
        }
        
        return $changed;
    }

    /**
     * Check if specific field has changed
     */
    public function hasFieldChanged(string $field): bool
    {
        return isset($this->formData[$field]) && 
               isset($this->originalData[$field]) && 
               $this->formData[$field] !== $this->originalData[$field];
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
        $this->resetErrorBag();
    }

    /**
     * Refresh model data
     */
    public function refresh(): void
    {
        $this->loadModel();
    }

    /**
     * Override resetForm to use original data
     */
    protected function resetForm(): void
    {
        $this->formData = $this->originalData;
        $this->resetErrorBag();
    }
}
