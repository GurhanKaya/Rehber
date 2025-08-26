<?php

namespace App\Livewire\Base;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

abstract class BaseFormComponent extends Component
{
    /**
     * Form data
     */
    public array $formData = [];

    /**
     * Form validation rules
     */
    protected array $rules = [];

    /**
     * Form validation messages
     */
    protected array $messages = [];

    /**
     * Form validation attributes
     */
    protected array $validationAttributes = [];

    /**
     * Success message
     */
    protected string $successMessage = '';

    /**
     * Error message
     */
    protected string $errorMessage = '';

    /**
     * Redirect route after success
     */
    protected ?string $redirectRoute = null;

    /**
     * Redirect parameters
     */
    protected array $redirectParams = [];

    /**
     * Initialize component
     */
    public function mount(): void
    {
        $this->initializeForm();
    }

    /**
     * Initialize form data
     */
    abstract protected function initializeForm(): void;

    /**
     * Validate form data
     */
    protected function validateForm(): array
    {
        try {
            return $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (ValidationException $e) {
            $this->addError('form', $this->errorMessage ?: 'Validation failed');
            Log::warning('Form validation failed', [
                'component' => static::class,
                'errors' => $e->errors()
            ]);
            throw $e;
        }
    }

    /**
     * Save form data
     */
    public function save(): void
    {
        try {
            $validatedData = $this->validateForm();
            
            $result = $this->processForm($validatedData);
            
            if ($result) {
                $this->showSuccessMessage();
                $this->resetForm();
                
                if ($this->redirectRoute) {
                    $this->redirect(route($this->redirectRoute, $this->redirectParams));
                }
            }
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Process form data
     */
    abstract protected function processForm(array $data): bool;

    /**
     * Show success message
     */
    protected function showSuccessMessage(): void
    {
        if ($this->successMessage) {
            session()->flash('success', $this->successMessage);
        }
    }

    /**
     * Handle errors
     */
    protected function handleError(\Exception $e): void
    {
        Log::error('Form processing failed', [
            'component' => static::class,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $this->addError('form', $this->errorMessage ?: 'An error occurred while processing the form');
    }

    /**
     * Reset form
     */
    protected function resetForm(): void
    {
        $this->formData = [];
        $this->resetErrorBag();
        $this->initializeForm();
    }

    /**
     * Update form data
     */
    public function updated($propertyName): void
    {
        $this->resetErrorBag($propertyName);
    }

    /**
     * Get validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set validation rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * Add validation rule
     */
    public function addRule(string $field, string $rule): void
    {
        $this->rules[$field] = $rule;
    }

    /**
     * Remove validation rule
     */
    public function removeRule(string $field): void
    {
        unset($this->rules[$field]);
    }

    /**
     * Check if form is valid
     */
    public function isFormValid(): bool
    {
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    /**
     * Get form errors count
     */
    public function getErrorsCount(): int
    {
        return count($this->getErrorBag()->getBag('default')->getMessages());
    }

    /**
     * Clear all form errors
     */
    public function clearErrors(): void
    {
        $this->resetErrorBag();
    }
}
