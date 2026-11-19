<?php

namespace Plugins\Posts\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Plugins\Posts\Models\PostAuthor;

class AuthorsManager extends Component
{
    use WithPagination;

    // Form Fields
    public $name = '';

    public $slug = '';

    public $email = '';

    public $bio = '';

    // Edit Mode
    public ?PostAuthor $editingAuthor = null;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'slug' => 'required|max:100',
        'email' => 'nullable|email|max:150',
        'bio' => 'nullable|string',
    ];

    public function render()
    {
        $authors = PostAuthor::withCount('posts')
            ->orderBy('name')
            ->paginate(20);

        return view('posts::livewire.authors-manager', [
            'authors' => $authors,
        ]);
    }

    public function updatedName($value)
    {
        if (! $this->editingAuthor) {
            $this->slug = Str::slug($value);
        }
    }

    public function store()
    {
        $this->validate(array_merge($this->rules, [
            'slug' => 'required|max:100|unique:post_authors,slug',
        ]));

        PostAuthor::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email ?: null,
            'bio' => $this->bio ?: null,
        ]);

        $this->reset(['name', 'slug', 'email', 'bio']);
        session()->flash('success', 'Author created successfully.');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Author created successfully.']);
    }

    public function edit($id)
    {
        $this->editingAuthor = PostAuthor::findOrFail($id);
        $this->name = $this->editingAuthor->name;
        $this->slug = $this->editingAuthor->slug;
        $this->email = $this->editingAuthor->email;
        $this->bio = $this->editingAuthor->bio;
    }

    public function update()
    {
        $this->validate(array_merge($this->rules, [
            'slug' => 'required|max:100|unique:post_authors,slug,'.$this->editingAuthor->id,
        ]));

        $this->editingAuthor->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email ?: null,
            'bio' => $this->bio ?: null,
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Author updated successfully.');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Author updated successfully.']);
    }

    public function cancelEdit()
    {
        $this->editingAuthor = null;
        $this->reset(['name', 'slug', 'email', 'bio']);
    }

    public function delete($id)
    {
        $author = PostAuthor::findOrFail($id);
        $authorName = $author->name;
        $author->delete();

        session()->flash('success', "Author '{$authorName}' deleted successfully.");
        $this->dispatch('notify', ['type' => 'success', 'message' => "Author '{$authorName}' deleted successfully."]);
    }
}
