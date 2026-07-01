@include('menus.form', [
    'title' => 'Edit Menu',
    'action' => route('menus.update', $menu),
    'method' => 'PUT',
    'menu' => $menu,
    'categories' => $categories,
])
