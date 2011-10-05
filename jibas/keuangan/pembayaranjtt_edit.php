<?
/**[N]**
 * JIBAS Road To Community
 * Jaringan Informasi Bersama Antar Sekolah
 * 
 * @version: 2.5.0 (Juni 20, 2011)
 * @notes: JIBAS Education Community will be managed by Yayasan Indonesia Membaca (http://www.indonesiamembaca.net)
 * 
 * Copyright (C) 2009 PT.Galileo Mitra Solusitama (http://www.galileoms.com)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 **[N]**/ ?>
<?
require_once('include/errorhandler.php');
require_once('include/sessionchecker.php');
require_once('include/common.php');
require_once('include/rupiah.php');
require_once('include/config.php');
require_once('library/jurnal.php');
require_once('include/db_functions.php');
require_once('include/theme.php');
require_once('include/sessioninfo.php');

$idpembayaran = $_REQUEST['idpembayaran'];

OpenDb();

$sql = "SELECT b.nis, b.besar, b.lunas, p.idbesarjtt, s.nama, p.idjurnal, p.jumlah, date_format(p.tanggal, '%d-%m-%Y') as tanggal, 
        		   p.keterangan, pn.nama as namapenerimaan, pn.rekkas, pn.rekpendapatan, pn.rekpiutang 
		    FROM penerimaanjtt p, besarjtt b, jbsakad.siswa s, datapenerimaan pn 
		   WHERE p.replid = $idpembayaran AND p.idbesarjtt = b.replid AND b.nis = s.nis AND b.idpenerimaan = pn.replid";
$row = FetchSingleArray($sql);

$nis = $row['nis'];
$nama = $row['nama'];
$idjurnal = $row['idjurnal'];
$tanggal = $row['tanggal'];
$keterangan = $row['keterangan'];
$namapenerimaan = $row['namapenerimaan'];
$besar = $row['jumlah'];
$idbesarjtt = $row['idbesarjtt'];
$besarjtt = $row['besar'];
$lunas = $row['lunas'];
$rekkas = $row['rekkas'];
$rekpiutang = $row['rekpiutang'];
$rekpendapatan = $row['rekpendapatan'];

$jbayar = $besar;
if (isset($_REQUEST['jcicilan']))
	$jbayar = $besar;
if (isset($_REQUEST['tcicilan']))
	$tanggal = $_REQUEST['tcicilan'];
if (isset($_REQUEST['kcicilan']))
	$keterangan = $_REQUEST['kcicilan'];	

if (1 == (int)$_REQUEST['issubmit']) 
{
	$jcicilan = UnformatRupiah($_REQUEST['jcicilan']);
	$tcicilan = $_REQUEST['tcicilan'];
	$tcicilan = MySqlDateFormat($tcicilan);
	$kcicilan = $_REQUEST['kcicilan'];
	$alasan = $_REQUEST['alasan'];
	$petugas = getUserName();
	
	if ($jcicilan == $besar) 
	{
		//--------------------------------------------------------------
		// Hanya mengubah informasi pembayaran tanpa mengubah besarnya  
		// -------------------------------------------------------------
		
		$sql = "UPDATE penerimaanjtt SET tanggal='$tcicilan', keterangan='$kcicilan', alasan='$alasan', petugas = '$petugas' WHERE replid=$idpembayaran";
		$result = QueryDb($sql);
		
		if ($result) 
		{
			CloseDb();
			echo  "<script language='javascript'>";
			echo  "opener.refresh();";
			echo  "window.close();";
			echo  "</script>";
			exit();
		} 
		else 
		{
			CloseDb();
			echo  "<script language='javascript'>";
			echo  "alert('Gagal mengubah data!');";
			echo  "</script>";
			exit();
		}
	} 
	else 
	{
		//----------------------------
		// Mengubah besar pembayaran  
		// ---------------------------
		
		$sql = "SELECT sum(jumlah) FROM penerimaanjtt WHERE idbesarjtt = $idbesarjtt AND replid <> $idpembayaran";
		$totalcicilan = FetchSingle($sql);
		
		$errmsg = "";
		if (($totalcicilan + $jcicilan) > $besarjtt) 
		{
			$errmsg = "Maaf, pembayaran tidak dapat dilakukan! Jumlah pembayaran cicilan lebih besar daripada bayaran yang harus dilunasi";
		}
		else
		{
			$lunas = 0;
			if (($totalcicilan + $jcicilan) == $besarjtt)
				$lunas = 1;
				
			$success = true;
			BeginTrans();	
			
			$sql = "UPDATE penerimaanjtt SET jumlah=$jcicilan, keterangan='$kcicilan', tanggal='$tcicilan', 
			        alasan='$alasan', petugas='$petugas' WHERE replid=$idpembayaran";
			QueryDbTrans($sql, $success);
			
			$idjurnal = 0;
			if ($success)
			{
				$sql = "SELECT idjurnal FROM penerimaanjtt WHERE replid = $idpembayaran";
				$idjurnal = FetchSingle($sql);
			}
			
			// update jurnaldetail
			if ($success)
			{
				$sql = "UPDATE jurnaldetail SET debet=$jcicilan WHERE idjurnal=$idjurnal AND koderek='$rekkas' AND kredit=0";
				QueryDbTrans($sql, $success);	
			}
			
			if ($success)
			{
				$sql = "UPDATE jurnaldetail SET kredit=$jcicilan WHERE idjurnal=$idjurnal AND koderek='$rekpiutang' AND debet=0";
				QueryDbTrans($sql, $success);	
			}
			
			if ($success)
			{
				$sql = "SET @DISABLE_TRIGGERS = 1;";
				QueryDb($sql);
				
				$sql = "UPDATE besarjtt SET lunas=$lunas WHERE replid=$idbesarjtt";
				QueryDbTrans($sql, $success);	
				
				$sql = "SET @DISABLE_TRIGGERS = NULL;";
				QueryDb($sql);
			}
			
			if ($success)
				CommitTrans();
			else
				RollbackTrans();
			CloseDb();
		}
		
		if (strlen($errmsg) == 0) 
		{
			if ($success) 
			{			
				echo  "<script language='javascript'>";
				echo  "opener.refresh();";
				echo  "window.close();";
				echo  "</script>";
				exit();
			} 
			else 
			{
				echo  "<script language='javascript'>";
				echo  "alert('Gagal menyimpan data!);";
				echo  "</script>";
			}
		}
	}
}
CloseDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="style/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style/calendar-green.css">
<link rel="stylesheet" type="text/css" href="style/tooltips.css">
<title>JIBAS KEU [Ubah Pembayaran Cicilan]</title>
<script src="script/SpryValidationTextField.js" type="text/javascript"></script>
<link href="script/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<script src="script/SpryValidationTextarea.js" type="text/javascript"></script>
<link href="script/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<script src="script/tooltips.js" language="javascript"></script>
<script language="javascript" src="script/tables.js"></script>
<script language="javascript" src="script/tools.js"></script>
<script language="javascript" src="script/rupiah.js"></script>
<script language="javascript" src="script/validasi.js"></script>
<script type="text/javascript" src="script/calendar.js"></script>
<script type="text/javascript" src="script/lang/calendar-en.js"></script>
<script type="text/javascript" src="script/calendar-setup.js"></script>
<script language="javascript">
function ValidateSubmit() 
{
	var isok = 	validateEmptyText('jcicilan', 'Besarnya Cicilan') &&
			 	validasiAngka() &&
		    	validateEmptyText('tcicilan', 'Tanggal Cicilan') &&
		    	validateEmptyText('alasan', 'Alasan Perubahan') &&
		    	validateMaxText('alasan', 500, 'Alasan Perubahan') &&
		    	validateMaxText('kcicilan', 255, 'Keterangan Cicilan') &&
		    	confirm('Data sudah benar?');	
	
	document.getElementById('issubmit').value = isok ? 1 : 0;
	
	if (isok)
		document.main.submit();
	else
		document.getElementById('Simpan').disabled = false;
}

function salert()
{
	if (confirm('Data sudah benar?'))
		return true;
}

function validasiAngka() 
{
	var angka = document.getElementById("angkacicilan").value;
	if(isNaN(angka)) 
	{
		alert ('Besarnya cicilan harus berupa bilangan!');
		document.getElementById('jcicilan').value = "";
		document.getElementById('jcicilan').focus();
		return false;
	}
	else if(angka < 0)
	{
		alert ('Besarnya cicilan tidak boleh negatif!');
		document.getElementById('jcicilan').focus();
		return false;
	}
	return true;
}

function salinangka()
{	
	var angka = document.getElementById("jcicilan").value;
	document.getElementById("angkacicilan").value = angka;
}

function focusNext(elemName, evt) 
{
    evt = (evt) ? evt : event;
    var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
    if (charCode == 13) 
	 {
		document.getElementById(elemName).focus();
        return false;
    }
    return true;
}
</script>
</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" background="" style='background-color:#dfdec9' onLoad="document.getElementById('jcicilan').focus();">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr height="58">
	<td width="28" background="<?=GetThemeDir() ?>bgpop_01.jpg">&nbsp;</td>
    <td width="*" background="<?=GetThemeDir() ?>bgpop_02a.jpg">
	<div align="center" style="color:#FFFFFF; font-size:16px; font-weight:bold">
    .: Ubah Pembayaran Cicilan :.
    </div>
	</td>
    <td width="28" background="<?=GetThemeDir() ?>bgpop_03.jpg">&nbsp;</td>
</tr>
<tr height="150">
	<td width="28" background="<?=GetThemeDir() ?>bgpop_04a.jpg">&nbsp;</td>
    <td width="0" style="background-color:#FFFFFF">
    
    <form name="main" method="post">
    <input type="hidden" name="issubmit" id="issubmit" value="0" />
    <input type="hidden" name="idpembayaran" id="idpembayaran" value="<?=$idpembayaran ?>" />
   	<table border="0" width="95%" cellpadding="2" cellspacing="2" align="center">
	<!-- TABLE CONTENT -->
    <tr>
        <td width="50%"><strong>Pembayaran</strong></td>
        <td colspan="2"><input type="text"  size="30" value="<?=$namapenerimaan?>" readonly style="background-color:#CCCC99"/></td>
    </tr>
    <tr>
        <td><strong>Nama</strong></td>
        <td colspan="2"><input type="text"  size="30" value="<?=$nis . " - " . $nama ?>" readonly style="background-color:#CCCC99"/></td>
    </tr>
    <tr>
        <td><strong>Jumlah Cicilan</strong></td>
        <td colspan="2"><input type="text" name="jcicilan" id="jcicilan" value="<?=FormatRupiah($jbayar) ?>" onblur="formatRupiah('jcicilan')" onfocus="unformatRupiah('jcicilan')" onKeyPress="return focusNext('alasan', event)" onkeyup="salinangka()"/>
        <input type="hidden" name="angkacicilan" id="angkacicilan" value="<?=$jbayar?>" />
        </td>
    </tr>
    <tr>
        <td><strong>Tanggal</strong></td>
        <td>
        <input type="text" name="tcicilan" id="tcicilan" readonly size="15" value="<?=$tanggal ?>" onKeyPress="return focusNext('alasan', event)" onClick="Calendar.setup()" style="background-color:#CCCC99"> </td>
        <td width="45%">
        <img src="images/calendar.jpg" name="tabel" border="0" id="btntanggal" onMouseOver="showhint('Buka kalendar!', this, event, '100px')"/>
	    </td>        
    </tr>
    <tr>
        <td valign="top"><strong>Alasan Perubahan</strong></td>
        <td colspan="2"><textarea id="alasan" name="alasan" rows="3" cols="30" onKeyPress="return focusNext('kcicilan', event)"><?=$alasan ?></textarea>
        </td>
    </tr>
    <tr>
        <td valign="top">Keterangan</td>
        <td colspan="2"><textarea id="kcicilan" name="kcicilan" rows="3" cols="30" onKeyPress="return focusNext('Simpan', event)"><?=$keterangan ?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
        <input type="button" name="Simpan" id="Simpan" class="but" value="Simpan" onclick="this.disabled = true; ValidateSubmit();" />
        <input type="button" name="tutup" id="tutup" class="but" value="Tutup" onclick="window.close()" />
        </td>
    </tr>
    </table>
    </form>
   
<!-- END OF CONTENT //--->
    </td>
    <td width="28" background="<?=GetThemeDir() ?>bgpop_06a.jpg">&nbsp;</td>
</tr>
<tr height="28">
	<td width="28" background="<?=GetThemeDir() ?>bgpop_07.jpg">&nbsp;</td>
    <td width="*" background="<?=GetThemeDir() ?>bgpop_08a.jpg">&nbsp;</td>
    <td width="28" background="<?=GetThemeDir() ?>bgpop_09.jpg">&nbsp;</td>
</tr>
</table>
<? if (strlen($errmsg) > 0) { ?>
<script language="javascript">
	alert('<?=$errmsg?>');		
</script>
<? } ?>
</body>
</html>
<script language="javascript">
  Calendar.setup(
    {
      //inputField  : "tanggalshow","tanggal"
	  inputField  : "tcicilan",         // ID of the input field
      ifFormat    : "%d-%m-%Y",    // the date format
      button      : "btntanggal"       // ID of the button
    }
   );
    Calendar.setup(
    {
      inputField  : "tcicilan",        // ID of the input field
      ifFormat    : "%d-%m-%Y",    // the date format	  
	  button      : "tcicilan"       // ID of the button
    }
  );
var sprytextfield1 = new Spry.Widget.ValidationTextField("tcicilan");
var sprytextfield1 = new Spry.Widget.ValidationTextField("jcicilan");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("kcicilan");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("alasan");
</script>