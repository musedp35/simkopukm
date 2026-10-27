<!DOCTYPE html>
<html>
<head>
<title><?=  $title ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Costumer Infomation Base</title>
    
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/formanggota.css')?>">
  
        <!-- CDN Materialize -->
        
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
       
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Document</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

        <!-- Compiled and minified JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        
         <!-- bootstrap 
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> -->
                <!-- JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        
</head>

<body>
<?php foreach ($anggota->result() as $row) ?>
<form name= "editanggota" onsubmit="return validateForm()" class= "col s12" action="<?= base_url('Anggota/simpanAnggota')."/".
                          $row->nomcib ?>" method="post">
        
        <h2 style= "text-align:center; color: black;" >Master Calon Anggota ALB</h2>
        <h4 style= "text-align:center; color: black;">Edit Form</h4>             
            <div class="container p-3 my-3 border">
                <div class="card-panel">
                    <div id="boxtitle">
                        <label id="lbtitle">Data Diri</label>
                    </div>

                    <div class="row">
                        
                    <div id="box" class ="col s6">
                        <label id= "txcib">Nomor Registrasi</label>
                        <!-- <input type="number" name="edtNoCib" placeholder="Masukan Nomor" /></td> -->
                        <label name="edtNoCib" id="txcib" for="edtNoCib"> <?=  $row->nomcib?></label>
                     </div>

                            <div id="box" class ="col s6">
                                <label id= "txsize">Nama Lengkap</label>
                                <input id="edtNama" value="<?= $row->nama?>" type="text" class="form-control" name="edtNama" placeholder="Masukan Nama"  />
                            </div>

                    </div>

                    <div class= "row">

                    
                        <div id="box" class="col s4">
                        <label  id = "txsize">NIK</label>
                        <input id="edtKotaLahir" id="edtNoId" value="<?= $row->nomor_id?>" type="number" name="edtNoId"  placeholder="Masukan No. Identitas"  />
                        </div>

                        <div id="box" class="col s4">
                                <label id="txsize">Tempat Lahir</label>
                                <input id="dtpTgllahir"  value="<?=  $row->tmp_lahir?>" type="text" name="edtKotaLahir"  placeholder="Masukan Kota" value="" />
                            </div>

                        <div id="box" class="col s4">
                                <label id="txsize">Tanggal Lahir</label>
                                <input value="<?=  $row->tgl_lahir?>" type="date" name="dtpTgllahir"  placeholder="DD-MM-YYYY" />  
                        </div>

                    </div>

                    <div class="row">
                    
                            <div id="box" class="col s4">
                                <label id="txsize"> Jenis Kelamin </label>
                                    <select class="browser-default" id="cmbJnsKel" name="cmbJnsKel" >
                                        <option value="" hidden>Pilih Jenis Kelamin</option>
                                        <option value="L"<?php if($row->jkl=="L") echo 'selected="selected"'; ?>>Laki-laki</option>
                                        <option value="P"<?php if($row->jkl=="P") echo 'selected="selected"'; ?>>Perempuan</option>
                                    </select>
                        
                            </div>

                            <div id="box" class="col s4">
                                <label id="txsize" for="cmbAgama"> Agama </label>
                                <select class="browser-default" id="cmbAgama" name="cmbAgama"  >
                                <option value="0" disabled selected>Pilih Agama</option>
                                <?php foreach ($agama->result_array() as $agm): ?>
                                    <option value="<?=$agm['kodeagm']?>" <?php echo $row->kodeagama === $agm['kodeagm'] ? 'selected="selected"'  : '' ?>>
                                            <?php echo $agm ['namaagm'] ?>
                                        </option> 
                                    <?php endforeach; ?> 
                                </select>
                            </div> 

                            <div id="box" class="col s4">
                                <label id="txsize">No. Telepon</label>
                                <input type="number" value="<?= $row->telp?>"  name="edtNoTelp"  placeholder="Masukan No. Telp" />
                            </div>

                    </div>


                    <div class="row">

                    <div id="box" class="col s6">
                            <label id="txsize" for="cmbPekerjaan"> Pekerjaan </label>
                                <select class="browser-default" id="cmbPekerjaan" name="cmbPekerjaan" >
                                <option value="<?= $pkj['pekerjaan'] = '' ? 0 :$row->kode?>" disabled selected>Pilih Pekerjaan</option>
                                    <?php foreach ($pekerjaan->result_array() as $pkj): ?>
        
                                        <option value="<?=$pkj['kode']?>" <?php echo $row->kode === $pkj['kode'] ? 'selected="selected"'  : '' ?>>
                                            <?php echo $pkj['pekerjaan'] ?>
                                        </option>  

                                    <?php endforeach; ?>
                                </select>
                            </div> 

                        <div id="box" class="col s6">
                            <label id="txsize">NPWP</label>
                            <input value ="<?= $row->npwp?>" type="number" name="edtNPWP" placeholder="Masukan NPWP" />
                        </div>

                    </div>
                    

                    <div class="row">
                        

                        <div class="col s6">
                        <label id="txsize">Alamat</label>
                        <input value ="<?= $row->alamat?>"  type="text" name="edtAlamat"  placeholder="Masukan Alamat" />
                        </div>

                        <div class="col s6">
                        <label id="txsize">Kota</label>
                        <input value="<?=  $row->kota?>" type="text" name="edtKota"  placeholder="Masukan Kota" />
                        </div>
                        
                    </div>


                    <div id="boxtitle">
                    <label id="lbtitle"> Keanggotaan </label>
                    </div>

                    <div class="row">
        
                            <!-- INPUTAN TGL MASUK ANGGOTA -->
                            <div id="box" class="col s12">
                            <label id="txsize">Tanggal Masuk</label>
                            <input type="date" id="dtpTglBuka" name="dtpTglBuka" placeholder="DD-MM-YYYY"  />
                        
                            </div>
                    </div>    
                    

                    <div id="boxtitle">
                        <label id="lbtitle">*Ahli Waris</label>  
                    </div> 
                            
                                <br>
                                <br>
                                <label id="txsize"> Nama</label>
                                <input value="<?=  $row->nama_aw?>" type="text" name="edtNamaAW" placeholder="Masukan Nama" />

                                <label id="txsize">Alamat</label>
                                <input value="<?=  $row->alm_aw?>" type="text" name="edtAlamatAW" placeholder="Masukan Alamat" />

                                <label id="txsize">Hubungan</label>
                                <input value="<?=  $row->hub_aw?>" type="text" name="edtHub" placeholder="Masukan Hubungan" />

                   
                                
                    <div id="btn">
                            <!-- <button id="simpan" onclick= "return validFunction()" class="waves-effect waves-light btn-large modal-trigger card-panel blue darken-4" type="submit" value="Validate" href="#modalsave"><i class="material-icons left">create</i>Simpan</button> 
                            
                            <button class="waves-effect waves-light btn-large card-panel red darken-4 " href="<?php echo base_url('anggota')?> type="reset"><i class="material-icons left">close</i>Batal</button> -->
                            <div class="center-align">
                                <a class="waves-effect waves-dark btn-large card-panel blue accent-2" href="<?php echo base_url('anggota')?>" type="reset">
                                <i class="material-icons left">arrow_back</i>Kembali</a>
                                
                                <button type="submit" onclick= "return validFunction()" class="waves-effect waves-light btn-large card-panel blue accent-3" value="Validate">
                                <i class="material-icons left">arrow_forward</i>Selanjutnya</a>
                            </div>
                    
                    <script> 
                                function validFunction() {

                                    if ($('#edtNama').val() == "") {
                                        alert("Nama tidak boleh kosong");  
                                        $( "#edtNama" ).focus();
                                        return false;  
                                    }
                                    if ($('#edtNoId').val() == "") {
                                        alert("NIK tidak boleh kosong");   
                                        $( "#edtNoId" ).focus();
                                        return false;
                                    }
                                    if ($('#edtKotaLahir').val() == "") {
                                        alert("Tempat Lahir tidak boleh kosong");   
                                        $( "#edtKotaLahir" ).focus();
                                        return false;
                                    }
                                    if ($('#dtpTgllahir').val() == "") {
                                        alert("Pilih Tanggal Lahir");   
                                        $( "#dtpTgllahir" ).focus();
                                        return false;
                                    }
                                    if (!$("#cmbJnsKel").val()) {
                                        alert("Pilih Jenis Kelamin");   
                                        $( "#cmbJnsKel" ).focus();
                                        return false;
                                    }
                                    if ($('#cmbAgama').val() == "00") {
                                        alert("Pilih Agama");   
                                        $( "#cmbAgama" ).focus();
                                        return false;
                                    }
                                    if ($('#cmbPekerjaan').val() == "0") {
                                        alert("Pilih Pekerjaan");   
                                        $( "#cmbPekerjaan" ).focus();
                                        return false;
                                    }
                                    if ($('#cmbJnsAnggota').val() == "") {
                                        alert("Pilih Jenis Keanggotaan");   
                                        $( "#cmbJnsAnggota" ).focus();
                                        return false;
                                    }
                                    if ($('#dtpTglBuka').val() == "") {
                                        alert("Pilih Tanggal Masuk");   
                                        $( "#dtpTglBuka" ).focus();
                                        return false;
                                    }else{
                                        // href="<//?php echo base_url('view_upload.php')?>"
                                        // var yakin = confirm("Apakah anda ingin menyimpan data?");
                                        // if (yakin) {
                                        // window.location="<?php //echo base_url('admin/view_uploads.php')?>";  
                                        return true;
                                        // } else {    
                                    }
                                  

                                }
                    </script>

                    </div>   
                </div>
            </div>                    
    </form>


</body>
</html>