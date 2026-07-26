<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all();
        return view('AdminPage.Faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'nullable|string',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'status_faqs' => Faq::FAQ_AKTIF,
        ]);

        return redirect()->route('faq')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'nullable|string',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        return redirect()->route('faq')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->status_faqs = $faq->status_faqs === Faq::FAQ_AKTIF
            ? Faq::FAQ_TIDAK_AKTIF
            : Faq::FAQ_AKTIF;

        $faq->save();

        return redirect()->route('faq')->with('success', 'Status FAQ berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->route('faq')->with('success', 'FAQ berhasil dihapus.');
    }
}
