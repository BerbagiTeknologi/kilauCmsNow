<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\SettingsMenu;
use Illuminate\Http\Request;

class SettingsMenuController extends Controller
{
    public function index()
    {
        $settingsMenus = SettingsMenu::all();
        return view('AdminPage.SettingsMenu.index', compact('settingsMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'subjudul' => 'nullable|string|max:255',
        ]);

        SettingsMenu::create([
            'judul' => $request->judul,
            'subjudul' => $request->subjudul,
            'status' => SettingsMenu::SETTINGS_MENU_AKTIF, // Default aktif
        ]);

        return redirect()->route('settingsmenu')->with('success', 'Settings menu berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'subjudul' => 'nullable|string|max:255',
        ]);

        $settingsMenu = SettingsMenu::findOrFail($id);
        $settingsMenu->update([
            'judul' => $request->judul,
            'subjudul' => $request->subjudul,
        ]);

        return redirect()->route('settingsmenu')->with('success', 'Settings menu berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:1,2',
        ]);

        $settingsMenu = SettingsMenu::findOrFail($id);
        $settingsMenu->status = $request->status;
        $settingsMenu->save();

        return redirect()->route('settingsmenu')->with('success', 'Status settings menu berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $settingsMenu = SettingsMenu::findOrFail($id);
        $settingsMenu->delete();

        return redirect()->route('settingsmenu')->with('success', 'Settings menu berhasil dihapus.');
    }
}
