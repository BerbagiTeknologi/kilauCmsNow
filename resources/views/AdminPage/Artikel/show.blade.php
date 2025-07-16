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

{{-- ganti id modal + carousel --}}
<div class="modal fade" id="showArticleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Article</h5>
           <button type="button"
                class="close force-modal-close"
                data-dismiss="modal">
            <span>&times;</span>
        </button>

      </div>

      <div class="modal-body">
        <div class="blog-details">

          {{-- CAROUSEL --}}
          <div id="carouselArticle" class="carousel slide mb-3" data-ride="carousel">
            <div class="carousel-inner" id="modal-images"></div>

            <a class="carousel-control-prev" href="#carouselArticle" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselArticle" role="button" data-slide="next">
              <span class="carousel-control-next-icon"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>

          <h3 id="modal-judul"  class="title mb-1"></h3>
          <p  id="modal-author" class="text-muted mb-3"></p>
         <p id="modal-kategori" class="mb-3">
          <i class="fas fa-folder-open text-primary mr-1"></i>
          <span id="kategori-text">-</span>
        </p>

          <div class="meta-top">
            <ul>
              <li><i class="fas fa-thumbs-up"></i></li>
              <li><i class="fas fa-comment"></i></li>
              <li><i class="fas fa-eye"></i></li>
            </ul>
          </div>

          <div id="modal-konten" class="ql-editor"></div>
          <div id="modal-tags" class="tags-container mb-3"></div>

        </div>
      </div>

      <div class="modal-footer">
            <button type="button"
            class="btn btn-secondary force-modal-close"
            data-dismiss="modal">Tutup</button>

      </div>

    </div>
  </div>
</div>

