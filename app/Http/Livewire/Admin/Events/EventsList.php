<?php

namespace App\Http\Livewire\Admin\Events;

use App\Models\Events;
use Livewire\Component;

class EventsList extends Component
{
    public $ipp = 20;
    public $modal = false;
    public Events $newItem;
    public $date;

    protected $rules = [
        'newItem.start_time' => 'required',
        'newItem.file_id' => 'required',
    ];

    public function mount($date = null)
    {
        $this->date = $date;
        if ($this->date == null) {
            $this->date = date("Y-m-d", time());
            if (date('Y-m-d H:i:s', strtotime($this->date . ' 06:00:00')) > date('Y-m-d H:i:s', time())) {
                $this->date = date("Y-m-d", strtotime('yesterday'));
            }
        } else {
            $this->date = date("Y-m-d", strtotime($this->date));
        }
    }
    public function render()
    {
        $items = $this->applyFilter(Events::query());

        return view('livewire.admin.events.events-list',[
            'items' => $items->paginate($this->ipp),
        ]);
    }


    private function applyFilter(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query;
    }

    public function openModal() {
        $this->newItem = new Events([
           'start_time' => date('d.m.Y H:i:s')
        ]);
        $this->modal=true;
    }


    public function closeModal() {
        $this->modal=false;
    }

    public function save()
    {
        $this->validate();
        $this->newItem->end_time = date('Y-m-d H:i:s',strtotime($this->newItem->start_time) + $this->newItem->fileItem->duration);

        $this->newItem->save();
        $this->modal = false;
    }
}
