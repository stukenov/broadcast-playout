<?php

namespace App\Http\Livewire\Admin\Files;

use App\Models\Files;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FilesList extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $file;
    public int $ipp = 20;
    public $modal = false;
    protected $rules = [
        'file' => 'required|file|'
    ];

    public function render()
    {
        $items = $this->applyFilter(Files::query());
        return view('livewire.admin.files.files-list',[
            'items' => $items->paginate($this->ipp),
        ]);
    }

    private function applyFilter(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query;
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function openModal()
    {
        $this->modal = true;
    }

    public function save()
    {
        

        $this->validate();
        $item = new Files([
            'name' => $this->file->getClientOriginalName(),
            'duration' => 0,
            'path' => $this->file->store('/'.date('Y').'/'.date('m').'/'.date('d'),'public'),
        ]);
        $item->save();
        $this->file = null;
        $this->modal = false;
    }

    public function destroy($id)
    {
        Files::find($id)->delete();
        $this->emit('successDelete');
    }
}
