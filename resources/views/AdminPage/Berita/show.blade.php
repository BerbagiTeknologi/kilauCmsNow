<style>
    /* Meta Top hanya ikon */
    .blog-details .meta-top {
        margin-bottom: 20px;
    }
    .blog-details .meta-top ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .blog-details .meta-top ul li {
        font-size: 1.2rem;
        color: #1363c6;
    }

    /* Pastikan gambar dalam konten Quill tampil full-width */
    .blog-details .content img,
    .blog-details .ql-editor img {
        max-width: 100% !important;
        height: auto !important;
        display: block;
    }

    /* Atur tampilan carousel gambar agar lebih besar dan proporsional */
    .carousel-item img {
        max-height: 600px; /* Lebih besar agar gambar lebih menonjol */
        object-fit: contain;
        border-radius: 10px;
        width: 100%;
    }

    /* Style untuk masing-masing tag link agar tampil seperti tombol kecil */
    .tag-link {
        display: inline-block;
        margin-left: 15px;
        padding: 5px 10px;
        font-size: 0.9rem;    /* Ukuran font kecil */
        color: #007bff;
        border: 1px solid #007bff;  /* Border tipis pada tiap tag */
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .tag-link:hover {
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
    }


</style>

<div class="modal fade" id="showBeritaModal" tabindex="-1" aria-labelledby="showBeritaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Berita</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="blog-details">
                    <!-- Carousel Gambar -->
                    <div class="post-img">
                        <div id="carouselBerita" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" id="modal-images">
                                <!-- Gambar akan disisipkan secara dinamis via JavaScript -->
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselBerita" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselBerita" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>

                    <!-- Judul Berita (ditempatkan di bawah carousel) -->
                    <h3 id="modal-judul" class="title mt-3"></h3>

                    <!-- Meta Top (Hanya ikon: Like, Comment, Views) -->
                    <div class="meta-top">
                        <ul>
                            <li>
                                <i class="fas fa-thumbs-up" title="Like"></i>
                            </li>
                            <li>
                                <i class="fas fa-comment" title="Comment"></i>
                            </li>
                            <li>
                                <i class="fas fa-eye" title="Views"></i>
                            </li>
                        </ul>
                    </div>

                    <!-- Konten Berita -->
                    <div id="modal-konten" class="ql-editor"></div>

                    <div id="modal-tags" class="tags-container">
                        <!-- Data tags akan disisipkan di sini melalui JavaScript -->
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
