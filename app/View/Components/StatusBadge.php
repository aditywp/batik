<?php
// app/View/Components/StatusBadge.php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusBadge extends Component
{
    public string $colorClass;

    public function __construct(public string $status, public string $label)
    {
        $this->colorClass = match ($status) {
            'pending'    => 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
            'processing' => 'bg-blue-50 text-blue-800 ring-1 ring-blue-200',
            'shipped'    => 'bg-purple-50 text-purple-800 ring-1 ring-purple-200',
            'delivered'  => 'bg-green-50 text-green-800 ring-1 ring-green-200',
            'cancelled'  => 'bg-red-50 text-red-800 ring-1 ring-red-200',
            'paid'       => 'bg-green-50 text-green-800 ring-1 ring-green-200',
            'unpaid'     => 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
            'refunded'   => 'bg-gray-50 text-gray-600 ring-1 ring-gray-200',
            default      => 'bg-stone-100 text-stone-600',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.status-badge');
    }
}