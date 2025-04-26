<?php

namespace App\Livewire\Admin;

use App\Models\SlaConfiguration;
use App\Models\Holiday;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class SlaConfigurationManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // SLA Configuration form fields
    public $editSlaId;
    public $warningHours;
    public $criticalHours;
    public $includeWeekends;

    // Holiday form fields
    public $editHolidayId;
    public $holidayName;
    public $holidayDate;
    public $holidayRecurring = false;

    // UI state
    public $isEditingSla = false;
    public $isEditingHoliday = false;
    public $isConfirmingDelete = false;
    public $deleteHolidayId;

    // Validation rules
    protected function rules()
    {
        return [
            'warningHours' => 'required|integer|min:1',
            'criticalHours' => 'required|integer|min:1|gt:warningHours',
            'includeWeekends' => 'boolean',
            'holidayName' => 'required|string|max:255',
            'holidayDate' => 'required|date',
            'holidayRecurring' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    public function render()
    {
        $slaConfigurations = SlaConfiguration::all();
        $holidays = Holiday::orderBy('date')->paginate(10);

        return view('livewire.admin.sla-configuration-manager', [
            'slaConfigurations' => $slaConfigurations,
            'holidays' => $holidays
        ])->layout('layouts.admin', ['title' => 'SLA Configuration']);
    }

    // SLA Configuration Methods

    public function editSla($id)
    {
        $this->resetInputFields();
        $this->isEditingSla = true;

        $slaConfig = SlaConfiguration::findOrFail($id);
        $this->editSlaId = $slaConfig->id;
        $this->warningHours = $slaConfig->warning_hours;
        $this->criticalHours = $slaConfig->critical_hours;
        $this->includeWeekends = $slaConfig->include_weekends;
    }

    public function updateSla()
    {
        $this->validate([
            'warningHours' => 'required|integer|min:1',
            'criticalHours' => 'required|integer|min:1|gt:warningHours',
        ]);

        $slaConfig = SlaConfiguration::findOrFail($this->editSlaId);

        $slaConfig->update([
            'warning_hours' => $this->warningHours,
            'critical_hours' => $this->criticalHours,
            'include_weekends' => $this->includeWeekends,
        ]);

        Log::info('SLA configuration updated', [
            'id' => $slaConfig->id,
            'stage' => $slaConfig->stage,
            'warning_hours' => $slaConfig->warning_hours,
            'critical_hours' => $slaConfig->critical_hours,
            'include_weekends' => $slaConfig->include_weekends,
        ]);

        session()->flash('message', 'SLA configuration updated successfully.');
        $this->resetInputFields();
        $this->isEditingSla = false;
    }

    // Holiday Methods

    public function createHoliday()
    {
        $this->resetInputFields();
        $this->isEditingHoliday = true;
    }

    public function editHoliday($id)
    {
        $this->resetInputFields();
        $this->isEditingHoliday = true;

        $holiday = Holiday::findOrFail($id);
        $this->editHolidayId = $holiday->id;
        $this->holidayName = $holiday->name;
        $this->holidayDate = $holiday->date->format('Y-m-d');
        $this->holidayRecurring = $holiday->recurring;
    }

    public function saveHoliday()
    {
        $this->validate([
            'holidayName' => 'required|string|max:255',
            'holidayDate' => 'required|date',
        ]);

        if ($this->editHolidayId) {
            // Update existing holiday
            $holiday = Holiday::findOrFail($this->editHolidayId);
            $holiday->update([
                'name' => $this->holidayName,
                'date' => $this->holidayDate,
                'recurring' => $this->holidayRecurring,
            ]);

            Log::info('Holiday updated', [
                'id' => $holiday->id,
                'name' => $holiday->name,
                'date' => $holiday->date->format('Y-m-d'),
                'recurring' => $holiday->recurring,
            ]);

            session()->flash('message', 'Holiday updated successfully.');
        } else {
            // Create new holiday
            $holiday = Holiday::create([
                'name' => $this->holidayName,
                'date' => $this->holidayDate,
                'recurring' => $this->holidayRecurring,
            ]);

            Log::info('Holiday created', [
                'id' => $holiday->id,
                'name' => $holiday->name,
                'date' => $holiday->date->format('Y-m-d'),
                'recurring' => $holiday->recurring,
            ]);

            session()->flash('message', 'Holiday created successfully.');
        }

        $this->resetInputFields();
        $this->isEditingHoliday = false;
    }

    public function confirmDelete($id)
    {
        $this->isConfirmingDelete = true;
        $this->deleteHolidayId = $id;
    }

    public function deleteHoliday()
    {
        $holiday = Holiday::findOrFail($this->deleteHolidayId);
        $holidayName = $holiday->name;

        $holiday->delete();

        Log::info('Holiday deleted', [
            'id' => $this->deleteHolidayId,
            'name' => $holidayName,
        ]);

        session()->flash('message', 'Holiday deleted successfully.');
        $this->isConfirmingDelete = false;
        $this->deleteHolidayId = null;
    }

    public function cancelDelete()
    {
        $this->isConfirmingDelete = false;
        $this->deleteHolidayId = null;
    }

    // Helper methods

    public function cancel()
    {
        $this->resetInputFields();
        $this->isEditingSla = false;
        $this->isEditingHoliday = false;
    }

    private function resetInputFields()
    {
        $this->editSlaId = null;
        $this->warningHours = null;
        $this->criticalHours = null;
        $this->includeWeekends = false;

        $this->editHolidayId = null;
        $this->holidayName = '';
        $this->holidayDate = '';
        $this->holidayRecurring = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
