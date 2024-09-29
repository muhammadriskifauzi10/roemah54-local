<?php

namespace App\Http\Controllers\Dashboard\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class MainController extends Controller
{
    public function index()
    {
        $data = [
            'judul' => 'Menu',
        ];

        return view('contents.dashboard.menu.main', $data);
    }
    public function datatablemenu()
    {
        $menus = Menu::with('children')
            ->select('menus.id', 'menus.name', 'menus.route', 'menus.parent_id', 'menus.order')
            ->join('menuroles', 'menus.id', '=', 'menuroles.menu_id')
            ->where('menuroles.role_id', auth()->user()->roles->pluck('id'))
            ->orderBy('menus.order', 'ASC')
            ->get();

        $output = [];
        $no = 1;

        foreach ($menus as $menu) {

            if (is_null($menu->parent_id)) {

                $menuaksi = '
                <a href="' . route('menu.edit', encrypt($menu->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001"/>
                    </svg>
                    Edit Menu
                </a>';

                $aksi = '
                <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                ' . $menuaksi . '
                </div>
                ';

                $output[] = [
                    'nomor' => "<strong>" . $no++ . "</strong>",
                    'name' => $menu->name,
                    'route' => $menu->route ? $menu->route : "-",
                    'parent_id' => 'Menu',
                    'order' => $menu->order,
                    'aksi' => $aksi,
                ];

                // Menambahkan submenu
                foreach ($menu->children as $child) {
                    $menuaksi = '
                    <a href="' . route('menu.edit', encrypt($child->id)) . '" class="btn btn-warning text-light fw-bold d-flex align-items-center justify-content-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                            <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001"/>
                        </svg>
                        Edit Menu
                    </a>';

                    $aksi = '
                    <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                    ' . $menuaksi . '
                    </div>
                    ';

                    $output[] = [
                        'nomor' => "<strong>" . $no++ . "</strong>",
                        'name' => '--- ' . $child->name, // Indentasi untuk submenu
                        'route' => $child->route ? "<a href='" . $child->route .
                            "' class='text-decoration-none fw-bold'>" . $child->route . "</a>" : "-",
                        'parent_id' => 'Menu Dari ' . $menu->name,
                        'order' => $child->order,
                        'aksi' => $aksi,
                    ];
                }
            }
        }

        return response()->json(['data' => $output]);
    }
    public function edit($id)
    {
        $id = decrypt($id);

        if (!Menu::where('id', $id)->exists()) {
            abort(404);
        }

        $menu = Menu::where('id', $id)->first();

        $data = [
            'judul' => 'Edit Menu',
            'menu' => $menu,
        ];

        return view('contents.dashboard.menu.edit', $data);
    }
    public function getmenufromreferensidari()
    {
        if (request()->ajax()) {
            $referensi_dari = request()->input('referensi_dari');
            $submenu = request()->input('submenu');
            if (Menu::where('id', (int)$referensi_dari)->exists()) {
                $optionmenu = [];
                foreach (Menu::where('parent_id', (int)$referensi_dari)->get() as $row) {
                    $optionmenu[] = '<option value="' . $row->id . '">' . $row->name . '</option>';
                }

                $response = [
                    'status' => 200,
                    'message' => 'success',
                    'data' => [
                        'dataHTML' => implode("", $optionmenu)
                    ]
                ];
            } else {
                $response = [
                    'status' => 400,
                    'message' => 'opps',
                ];
            }
        }

        return response()->json($response);
    }
}
