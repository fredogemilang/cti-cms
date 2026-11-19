<?php

namespace Plugins\RmaXyora\Livewire;

use App\Models\Form;
use App\Models\FormEntry;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\RmaXyora\Support\RmaStatus;

class RmaTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $perPage = 10;

    // Detail Modal State
    public $selectedRmaId = null;

    public $showModal = false;

    public $newStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Get the RMA form instance.
     */
    protected function getRmaForm()
    {
        return Form::where('slug', 'rma-form')->first();
    }

    /**
     * Get RMA requests query.
     */
    public function getRmaRequestsProperty()
    {
        $form = $this->getRmaForm();

        if (! $form) {
            return FormEntry::whereRaw('1 = 0'); // Empty relation
        }

        return FormEntry::query()
            ->where('form_id', $form->id)
            ->when($this->search, function ($query) {
                $cleanSearch = trim($this->search);
                $numericSearch = preg_replace('/[^0-9]/', '', $cleanSearch);

                $query->where(function ($q) use ($cleanSearch, $numericSearch) {
                    $q->where('data->nama_lengkap', 'like', '%'.$cleanSearch.'%')
                        ->orWhere('data->alamat_email', 'like', '%'.$cleanSearch.'%')
                        ->orWhere('data->serial_number_produk', 'like', '%'.$cleanSearch.'%')
                        ->orWhere('data->nama_produk', 'like', '%'.$cleanSearch.'%');

                    if ($numericSearch !== '') {
                        $q->orWhere('id', (int) $numericSearch);
                    }
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest();
    }

    /**
     * View detail of a specific RMA.
     */
    public function selectRma($id)
    {
        $rma = FormEntry::findOrFail($id);
        $this->selectedRmaId = $rma->id;
        $this->newStatus = $rma->status;
        $this->showModal = true;
    }

    /**
     * Close the detail modal.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedRmaId = null;
    }

    /**
     * Update the status of the selected RMA.
     */
    public function updateStatus()
    {
        if (! $this->selectedRmaId) {
            return;
        }

        $rma = FormEntry::findOrFail($this->selectedRmaId);
        $oldStatus = $rma->status;

        if ($this->newStatus !== $oldStatus) {
            $rma->status = $this->newStatus;
            $rma->save();

            session()->flash('success', 'Status RMA #'.sprintf('RMA-%04d', $rma->id).' berhasil diperbarui.');
        }

        $this->closeModal();
    }

    /**
     * Delete an RMA request.
     */
    public function deleteRma($id)
    {
        $rma = FormEntry::findOrFail($id);
        $rma->delete();

        session()->flash('success', 'Pengajuan RMA #'.sprintf('RMA-%04d', $id).' berhasil dihapus.');

        if ($this->selectedRmaId === $id) {
            $this->closeModal();
        }
    }

    /**
     * Get count of entries by status for badges.
     */
    public function getStatusCountsProperty()
    {
        $form = $this->getRmaForm();
        if (! $form) {
            return ['all' => 0];
        }

        $base = FormEntry::where('form_id', $form->id);
        $counts = [
            'all' => (clone $base)->count(),
        ];

        foreach (array_keys(RmaStatus::all()) as $key) {
            $counts[$key] = (clone $base)->where('status', $key)->count();
        }

        // Support legacy processing count if exists
        $processingCount = (clone $base)->where('status', 'processing')->count();
        if ($processingCount > 0) {
            $counts['processing'] = $processingCount;
        }

        return $counts;
    }

    public function render()
    {
        $entries = $this->rmaRequests->paginate($this->perPage);
        $selectedRma = $this->selectedRmaId ? FormEntry::find($this->selectedRmaId) : null;

        return view('rma-xyora::livewire.rma-table', [
            'entries' => $entries,
            'selectedRma' => $selectedRma,
            'statusCounts' => $this->statusCounts,
        ]);
    }
}
