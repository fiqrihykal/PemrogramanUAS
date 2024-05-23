<?php
session_start();
require "../session.php";
require "../functions.php";

if ($role !== 'Admin') {
  header("location:../login.php");
}

// Pagination
$jmlHalamanPerData = 5;
$jumlahData = count(query("SELECT sewa.id_sewa,user.nama_lengkap,sewa.tanggal_pesan,sewa.jam_mulai,sewa.lama_sewa,sewa.total,bayar.bukti,bayar.konfirmasi
FROM sewa
JOIN user ON sewa.id_user = user.id_user
JOIN bayar ON sewa.id_sewa = bayar.id_sewa"));
$jmlHalaman = ceil($jumlahData / $jmlHalamanPerData);

if (isset($_GET["halaman"])) {
  $halamanAktif = $_GET["halaman"];
} else {
  $halamanAktif = 1;
}

$awalData = ($jmlHalamanPerData * $halamanAktif) - $jmlHalamanPerData;

$pesan = query("SELECT sewa.id_sewa,user.nama_lengkap,sewa.tanggal_pesan,sewa.jam_mulai,sewa.lama_sewa,sewa.total,bayar.bukti,bayar.konfirmasi
FROM sewa
JOIN user ON sewa.id_user = user.id_user
JOIN bayar ON sewa.id_sewa = bayar.id_sewa LIMIT $awalData, $jmlHalamanPerData");

$bulan_tes =array(
  '01'=>"Januari",
  '02'=>"Februari",
  '03'=>"Maret",
  '04'=>"April",
  '05'=>"Mei",
  '06'=>"Juni",
  '07'=>"Juli",
  '08'=>"Agustus",
  '09'=>"September",
  '10'=>"Oktober",
  '11'=>"November",
  '12'=>"Desember"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

  <title>Data Pesanan</title>
  
</head>

<body>
  <div class="container-fluid">
    <div class="row min-vh-100">
      <div class="sidebar col-2 bg-secondary">
        <!-- Sidebar -->
        <h5 class="mt-5 judul text-center"><?= $_SESSION["username"]; ?></h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item bg-transparent"><a href="home.php">Home</a></li>
          <li class="list-group-item bg-transparent"><a href="member.php">Data Member</a></li>
          <li class="list-group-item bg-transparent"><a href="lapangan.php">Data Lapangan</a></li>
          <li class="list-group-item bg-transparent"><a href="pesan.php">Data Pesanan</a></li>
          <li class="list-group-item bg-transparent"><a href="laporan.php">Laporan Pesanan</a></li>
          <li class="list-group-item bg-transparent"><a href="admin.php">Data Admin</a></li>
          <li class="list-group-item bg-transparent"></li>
        </ul>
        <a href="../logout.php" class="mt-5 btn btn-inti text-dark">Logout</a>
      </div>
      <div class="col-10 p-5 mt-5">
        <!-- Konten -->

        <div class="col-md-12">
     <h4>
       <!--<a  style="padding-left:2pc;" href="fungsi/hapus/hapus.php?laporan=jual" onclick="javascript:return confirm('Data Laporan akan di Hapus ?');">
						<button class="btn btn-danger">RESET</button>
					</a>-->
       <?php if(!empty($_GET['cari'])){ ?>
       Data Laporan Penjualan <?= $bulan_tes[$_POST['bln']];?> <?= $_POST['thn'];?>
       <?php }elseif(!empty($_GET['hari'])){?>
       Data Laporan Penjualan <?= $_POST['hari'];?>
       <?php }else{?>
       Data Laporan Penjualan <?= $bulan_tes[date('m')];?> <?= date('Y');?>
       <?php }?>
     </h4>
     <br />
     <div class="card">
       <div class="card-header">
         <h5 class="card-title mt-2">Cari Laporan Per Bulan</h5>
       </div>
       <div class="card-body p-0">
         <form method="post" action="laporan.php?page=laporan&cari=ok">
           <table class="table table-striped">
             <tr>
               <th>
                 Pilih Bulan
               </th>
               <th>
                 Pilih Tahun
               </th>
               <th>
                 Aksi
               </th>
             </tr>
             <tr>
               <td>
                 <select name="bln" class="form-control">
                   <option selected="selected">Bulan</option>
                   <?php
								$bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
								$jlh_bln=count($bulan);
								$bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
								$no=1;
								for($c=0; $c<$jlh_bln; $c+=1){
									echo"<option value='$bln1[$c]'> $bulan[$c] </option>";
								$no++;}
							?>
                 </select>
               </td>
               <td>
                 <?php
								$now=date('Y');
								echo "<select name='thn' class='form-control'>";
								echo '
								<option selected="selected">Tahun</option>';
								for ($a=2017;$a<=$now;$a++)
								{
									echo "<option value='$a'>$a</option>";
								}
								echo "</select>";
							?>
               </td>
               <td>
                 <input type="hidden" name="periode" value="ya">
                 <button class="btn btn-primary">
                   <i class="fa fa-search"></i> Cari
                 </button>
                 <a href="index.php?page=laporan" class="btn btn-success">
                   <i class="fa fa-refresh"></i> Refresh</a>

                 <?php if(!empty($_GET['cari'])){?>
                 <a href="excel.php?cari=yes&bln=<?=$_POST['bln'];?>&thn=<?=$_POST['thn'];?>" class="btn btn-info"><i
                     class="fa fa-download"></i>
                   Excel</a>
                 <?php }else{?>
                 <a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i>
                   Excel</a>
                 <?php }?>
               </td>
             </tr>
           </table>
         </form>
         <form method="post" action="index.php?page=laporan&hari=cek">
           <table class="table table-striped">
             <tr>
               <th>
                 Pilih Hari
               </th>
               <th>
                 Aksi
               </th>
             </tr>
             <tr>
               <td>
                 <input type="date" value="<?= date('Y-m-d');?>" class="form-control" name="hari">
               </td>
               <td>
                 <input type="hidden" name="periode" value="ya">
                 <button class="btn btn-primary">
                   <i class="fa fa-search"></i> Cari
                 </button>
                 <a href="laporan.php?page=laporan" class="btn btn-success">
                   <i class="fa fa-refresh"></i> Refresh</a>

                 <?php if(!empty($_GET['hari'])){?>
                 <a href="excel.php?hari=cek&tgl=<?= $_POST['hari'];?>" class="btn btn-info"><i
                     class="fa fa-download"></i>
                   Excel</a>
                 <?php }else{?>
                 <a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i>
                   Excel</a>
                 <?php }?>
               </td>
             </tr>
           </table>
         </form>
       </div>
     </div>


     <table class="table table-hover mt-3">
          <thead class="table-inti">
            <tr>
              <th scope="col">No</th>
              <th scope="col">Nama</th>
              <th scope="col">Tanggal Pesan</th>
              <th scope="col">Tanggal Main</th>
              <th scope="col">Lama Main</th>
              <th scope="col">Total Harga</th>
              <th scope="col">Sudah Bayar</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody class="text">
            <?php $i = 1; ?>
            <?php foreach ($pesan as $row) : ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= $row["nama_lengkap"]; ?></td>
                <td><?= $row["tanggal_pesan"]; ?></td>
                <td><?= $row["jam_mulai"]; ?></td>
                <td><?= $row["lama_sewa"]; ?></td>
                <td><?= $row["total"]; ?></td>
                <td><?= $row["konfirmasi"]; ?></td>
                <td>
                  <?php
                  $id_sewa = $row["id_sewa"];
                  if ($row["konfirmasi"] == "konfirmasi") 
                    
                  ?>
                </td>
              </tr>
              
            <?php endforeach; ?>
          </tbody>
        </table>

        <ul class="pagination">
          <?php if ($halamanAktif > 1) : ?>
            <li class="page-item">
              <a href="?halaman=<?= $halamanAktif - 1; ?>" class="page-link">Previous</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $jmlHalaman; $i++) : ?>
            <?php if ($i == $halamanAktif) : ?>
              <li class="page-item active"><a class="page-link" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
            <?php else : ?>
              <li class="page-item "><a class="page-link" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($halamanAktif < $jmlHalaman) : ?>
            <li class="page-item">
              <a href="?halaman=<?= $halamanAktif + 1; ?>" class="page-link">Next</a>
            </li>
          <?php endif; ?>
        </ul>

      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>

</html>