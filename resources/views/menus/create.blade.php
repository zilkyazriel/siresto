@include('menus.form', [
    'title' => 'Tambah Menu Baru',
    'action' => route('menus.store'),
    'method' => 'POST',
    'menu' => null,
    'categories' => $categories,
])
