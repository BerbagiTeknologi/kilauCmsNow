@php
    $groupedGaleri = $galeri->groupBy('judul_kegiatan');
@endphp

@foreach ($groupedGaleri as $judul => $items)
    @php
        $firstItem = $items->first();
        $imageUrls = $items->flatMap(function($item) {
            return collect($item->file_galeri)->map(function($image) {
                return asset('storage/' . $image);
            });
        })->toArray();
    @endphp

    <div class="gallery-item" data-title="{{ $judul }}"
        data-images="{{ json_encode($imageUrls) }}"
        data-description="{{ $firstItem->deskripsi_kegiatan }}"
        data-cabang="{{ $firstItem->nama_kantor_cabang }}">
        <img src="{{ $imageUrls[0] }}" alt="{{ $judul }}" />
        <div class="gallery-caption">{{ $judul }}</div>
    </div>
@endforeach
