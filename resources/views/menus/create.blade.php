@include('menus.form', [
    'title' => 'Tambah Menu',
    'action' => route('menus.store'),
    'method' => 'POST',
    'menu' => null,
    'categories' => $categories,
    'stocks' => $stocks,
])