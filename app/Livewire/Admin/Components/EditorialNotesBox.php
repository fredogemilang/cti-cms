<?php

namespace App\Livewire\Admin\Components;

use App\Models\EditorialNote;
use Livewire\Component;

class EditorialNotesBox extends Component
{
    public string $notableType = '';

    public int $notableId = 0;

    public string $newNote = '';

    public function mount(string $notableType, int $notableId)
    {
        $this->notableType = $notableType;
        $this->notableId = $notableId;
    }

    public function addNote()
    {
        $this->validate([
            'newNote' => 'required|string|max:1000',
        ]);

        EditorialNote::create([
            'notable_type' => $this->notableType,
            'notable_id' => $this->notableId,
            'user_id' => auth()->id(),
            'note' => $this->newNote,
        ]);

        $this->newNote = '';
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Editorial note added.']);
    }

    public function deleteNote(int $noteId)
    {
        $note = EditorialNote::where('notable_type', $this->notableType)
            ->where('notable_id', $this->notableId)
            ->findOrFail($noteId);

        if ($note->user_id === auth()->id() || auth()->user()->hasRole('super-admin')) {
            $note->delete();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Note deleted.']);
        }
    }

    public function render()
    {
        $notes = EditorialNote::with('user')
            ->where('notable_type', $this->notableType)
            ->where('notable_id', $this->notableId)
            ->latest()
            ->get();

        return view('livewire.admin.components.editorial-notes-box', [
            'notes' => $notes,
        ]);
    }
}
