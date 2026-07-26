<?php

namespace App\Http\Controllers\AdminPage;

use App\Models\TimlineKilau;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    public function index()
    {
        $timeline = TimlineKilau::all();
        return view('AdminPage.Timeline.index', compact('timeline'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_timline' => 'required|string|max:255',
            'subjudul_timline' => 'required|string|max:255',
            'deskripsi_timeline' => 'nullable|string|max:500',
            'sequence_timeline' => 'nullable|integer',
            'selected_icon' => 'nullable|string', 
        ]);

        $timeline = new TimlineKilau();
        $timeline->judul_timline = $request->judul_timline;
        $timeline->subjudul_timline = $request->subjudul_timline;
        $timeline->status_timline = $request->status_timline;
        $timeline->deskripsi_timeline = $request->deskripsi_timeline;
        $timeline->sequence_timeline = $request->sequence_timeline;
        $timeline->status_timline = TimlineKilau::TIMELINE_AKTIF; // Status default

        if ($request->has('selected_icon')) {
            $timeline->icon_timeline = $request->selected_icon;
        }

        $timeline->save();

        return redirect()->route('timeline')->with('success', 'Timeline berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_timline' => 'required|string|max:255',
            'subjudul_timline' => 'required|string|max:255',
            'deskripsi_timeline' => 'nullable|string|max:500',
            'sequence_timeline' => 'nullable|integer',
            'selected_icon' => 'nullable|string',
        ]);

        $timeline = TimlineKilau::findOrFail($id);
        $timeline->judul_timline = $request->judul_timline;
        $timeline->subjudul_timline = $request->subjudul_timline;
        $timeline->deskripsi_timeline = $request->deskripsi_timeline;
        $timeline->sequence_timeline = $request->sequence_timeline;

        if ($request->has('selected_icon')) {
            $timeline->icon_timeline = $request->selected_icon;
        }

        $timeline->save();

        return redirect()->route('timeline')->with('success', 'Pimpinan berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'status_timline' => 'required|in:1,2', 
        ]);

        $timeline = TimlineKilau::findOrFail($id);
        $timeline->status_timline = $request->status_timline;
        $timeline->save();

        return redirect()->route('timeline')->with('success', 'Status timeline berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // Mengambil data timeline yang akan dihapus
        $timeline = TimlineKilau::findOrFail($id);

        if ($timeline->icon_timeline && Storage::disk('public')->exists($timeline->icon_timeline)) {
            Storage::disk('public')->delete($timeline->icon_timeline);
        }

        $timeline->delete();

        // Mengarahkan kembali dengan pesan sukses
        return redirect()->route('timeline')->with('success', 'Timeline berhasil dihapus.');
    }
}
