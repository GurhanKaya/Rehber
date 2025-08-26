<?php //modellerin çalışması için gerekli fonksiyonlar. modellerde şu pop up gibi olanlar.

namespace App\Livewire\Traits;

trait WithAdminModal
{
    public $showModal = false;
    public $editId = null;

    public function showCreateModal()
    {
        $this->editId = null;
        $this->resetFields();
        $this->setDefaultValues();
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $this->editId = $id;
        $this->loadForEdit($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editId = null;
        $this->resetFields();
    }

    public function save()
    {
        $this->validate();
        
        try {
            if ($this->editId) {
                $this->updateRecord();
                session()->flash('success', $this->getUpdateSuccessMessage());
            } else {
                $this->createRecord();
                session()->flash('success', $this->getCreateSuccessMessage());
            }
            
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'İşlem sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $this->deleteRecord($id);
            session()->flash('success', $this->getDeleteSuccessMessage());
        } catch (\Exception $e) {
            session()->flash('error', 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    // Abstract methods - override in components
    protected function resetFields()
    {
        // Override in component
    }

    protected function setDefaultValues()
    {
        // Override in component
    }

    protected function loadForEdit($id)
    {
        // Override in component
    }

    protected function createRecord()
    {
        // Override in component
    }

    protected function updateRecord()
    {
        // Override in component
    }

    protected function deleteRecord($id)
    {
        // Override in component
    }

    protected function getCreateSuccessMessage(): string
    {
        return 'Kayıt başarıyla oluşturuldu.';
    }

    protected function getUpdateSuccessMessage(): string
    {
        return 'Kayıt başarıyla güncellendi.';
    }

    protected function getDeleteSuccessMessage(): string
    {
        return 'Kayıt başarıyla silindi.';
    }
} 