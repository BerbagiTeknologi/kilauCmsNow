<style>
    #testimonial-scroll-container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 1rem;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
    }

    #testimonial-scroll-container::-webkit-scrollbar {
        height: 6px;
    }

    #testimonial-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .testimonial-card {
        width: 410px;
        min-width: 410px;
        border-radius: 0.80rem;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        scroll-snap-align: start;
        transition: transform 0.3s ease;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .testimonial-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        padding: 1rem;
    }
</style>

<div class="container-fluid bg-light py-5">
    <div class="container py-5">
        <div class="mx-auto text-center" style="max-width: 500px;">
            <h1 class="mb-4">{{ $testimoniMenu->judul }}</h1>
            <p class="lead">{{ $testimoniMenu->subjudul }}</p>
        </div>

        <div id="testimonial-scroll-container">
            @if ($testimonis && $testimonis->count())
                @foreach ($testimonis as $testimoni)
                    <div class="testimonial-card">
                        @if ($testimoni->video_link)
                            @php $embedURL = getEmbedURL($testimoni->video_link); @endphp
                            @if ($embedURL)
                                <div class="card-img-top">
                                    <iframe class="w-100" height="250" src="{{ $embedURL }}"
                                            title="YouTube video" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                </div>
                            @endif
                        @endif

                        <div class="testimonial-body">
                            <div class="d-flex align-items-center mb-3">
                                <img class="rounded-circle me-3"
                                     src="{{ $testimoni->file ? Storage::url($testimoni->file) : asset('assets/img/profile.jpg') }}"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <div style="max-width: 270px; overflow: hidden;">
                                        <h5 class="mb-0 text-truncate">{{ $testimoni->nama }}</h5>
                                    </div>
                                    
                                    <small>{{ $testimoni->pekerjaan }}</small>
                                </div>
                            </div>

                            <p class="card-text">{{ strip_tags($testimoni->komentar) ?: 'Tidak ada komentar.' }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center">Belum ada testimoni yang aktif.</p>
            @endif
        </div>
    </div>
</div>