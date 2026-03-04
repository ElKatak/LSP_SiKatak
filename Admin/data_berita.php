<?php
include "template/header.php";
include "template/menu.php";
?>
 <main class="app-main">
 <!--begin::App Content Header-->
 <div class="app-content-header">
 <!--begin::Container-->
 <div class="container-fluid">
 <!--begin::Row-->
 <div class="row">
 <div class="col-sm-6"><h3 class="mb-0">Data Berita</h3></div>
 <div class="col-sm-6">
 <ol class="breadcrumb float-sm-end">
 <li class="breadcrumb-item"><a href="#">Home</a></li>
 <li class="breadcrumb-item active" aria-current="page">Data Berita</li>
 </ol>
 </div>
 </div>
 <!--end::Row-->
 </div>
 <!--end::Container-->
 </div>
 <!--end::App Content Header-->
 <!--begin::App Content-->
 <div class="app-content">
 <!--begin::Container-->
 <div class="container-fluid">
 <!--begin::Row-->
 <div class="row">
 <div class="col-12">
 <!-- Default box -->
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Title</h3>
 <div class="card-tools">
 <button
 type="button"
 class="btn btn-tool"
 data-lte-toggle="card-collapse"
 title="Collapse"
 >
 <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
 <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
 </button>
 <button
 type="button"
 class="btn btn-tool"
 data-lte-toggle="card-remove"
 title="Remove"
 >
 <i class="bi bi-x-lg"></i>
 </button>
 </div>
 </div>
 <div class="card-body">
<!--end::App Content Header-->
 <!--begin::App Content-->
 <div class="app-content">
 <!--begin::Container-->
 <div class="container-fluid">
 <!--begin::Row-->
 <div class="row">
 <div class="col-md-12">
 <div class="card mb-10">
 <div class="card-header"><h3 class="card-title">Data Kontak</h3></div>
 <!-- /.card-header -->
 <div class="card-body">
 <table class="table table-bordered">
 <thead>
 <tr>
 <th style="width: 10px">No</th>
 <th>Judul</th>
 <th>Isi Berita</th>
 <th>Gambar</th>
 <th>Tanggal</th>
 <th>Penulisss</th>
 <th >Aksi</th>
 </tr>
 </thead>
 <tbody>
 <?php
 include '../koneksi.php';
 $no = 1;
 $data = mysqli_query($koneksi,"select * from berita");
 while($d = mysqli_fetch_array($data)){
 ?>
 <tr class="align-middle">
 <td><?php echo $no++; ?></td>
 <td><?php echo $d['judul']; ?></td>
 <td><?php echo $d['isi']; ?></td>
 <td><img src="upload/<?php echo $d['gambar']; ?>" width="80" class="imgthumbnail"></td>
 <td><?php echo $d['tanggal']; ?></td>
 <td><?php echo $d['penulis']; ?></td>
 <td class="text-center">
 <a href="edit_berita.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-warning me-1" title="Edit"> <i
class="bi bi-pencil-square"></i></a>
 <a href="hapus_berita.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-danger" title="Hapus"
onclick="return confirm('Yakin ingin menghapus data ini?')">
 <i class="bi bi-trash"></i></a>
</td>
 </tr>
 <?php
 }
 ?>
 </tbody>
 </table>
 </div>
 <!-- /.card-body -->
 <div class="card-footer clearfix">
 <ul class="pagination pagination-sm m-0 float-end">
 <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
 <li class="page-item"><a class="page-link" href="#">1</a></li>
 <li class="page-item"><a class="page-link" href="#">2</a></li>
 <li class="page-item"><a class="page-link" href="#">3</a></li>
 <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
 </ul>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
 <!-- /.card-body -->
 <div class="card-footer">Footer</div>
 <!-- /.card-footer-->
 </div>
 <!-- /.card -->
 </div>
 </div>
 <!--end::Row-->
 </div>
 </div>
 <!--end::App Content-->
 </main>
 <?php
 include "template/footer.php";
 ?>