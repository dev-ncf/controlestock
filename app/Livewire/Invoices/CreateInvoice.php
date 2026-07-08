<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Produto;

class CreateInvoice extends Component
{
    public $search = ''; 
    public $cart = [];

    public function addItem($id) {
        $p = Produto::find($id);
        if(!$p) return;

        $this->cart[] = [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->sale_price,
            'qty' => 1
        ];
        $this->search = ''; 
    }

    public function render() {
        // LÓGICA PROATIVA:
        if (empty($this->search)) {
            // Se não houver pesquisa, mostra os produtos que têm mais stock
            $products = Produto::orderBy('stock_quantity', 'desc')->limit(5)->get();
            $isSearch = false;
        } else {
            // Se houver pesquisa, mostra os resultados
            $products = Produto::where('name', 'like', "%{$this->search}%")
                               ->orWhere('sku', 'like', "%{$this->search}%")
                               ->get();
            $isSearch = true;
        }

        return view('components.invoices.create-invoice', [
            'products' => $products,
            'isSearch' => $isSearch
        ]);
    }
}