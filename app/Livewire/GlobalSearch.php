<?php

namespace App\Livewire;

use App\Models\EthicsRequest;
use Livewire\Component;

/**
 * Global search dropdown component in top navigation.
 * Searches across study titles, generated control numbers, and student author names.
 */
class GlobalSearch extends Component
{
    public string $query = '';

    public function render()
    {
        $results = [];

        if (strlen($this->query) >= 2) {
            $term = '%'.$this->query.'%';

            $results = EthicsRequest::with(['student', 'department'])
                ->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('control_number', 'like', $term)
                        ->orWhereHas('student', function ($sq) use ($term) {
                            $sq->where('name', 'like', $term);
                        });
                })
                ->limit(6)
                ->get();
        }

        return view('livewire.global-search', [
            'results' => $results,
        ]);
    }
}
