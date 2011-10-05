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
require_once('../include/errorhandler.php');
require_once('../include/sessioninfo.php');
require_once('../include/common.php');
require_once('../include/config.php');
require_once('../include/db_functions.php');
require_once('../sessionchecker.php');

if(isset($_REQUEST["kelas"]))
	$kelas = $_REQUEST["kelas"];
if(isset($_REQUEST["semester"]))
	$semester = $_REQUEST["semester"];
if(isset($_REQUEST["pelajaran"]))
	$pelajaran = $_REQUEST["pelajaran"];

OpenDb();
$sql="SELECT k.kelas, p.nama FROM kelas k, pelajaran p WHERE p.replid=$pelajaran AND k.replid=$kelas";
$result=QueryDb($sql);
$row = mysql_fetch_array($result);
$namakelas = $row['tingkat'];
$namapelajaran = $row['nama'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Menu</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../style/style.css">
<script language="JavaScript" src="../script/tables.js"></script>
<script language="JavaScript">

function pilih(kelas,rpp){
	parent.content.location.href="ujian_rpp_siswa_content.php?kelas="+kelas+"&rpp="+rpp;
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<?
//$query_aturan = "SELECT DISTINCT u.idrpp, r.rpp FROM ujian u, rpp r, kelas k WHERE u.idrpp = r.replid AND r.idtingkat = k.idtingkat AND k.replid = $kelas AND r.idsemester = $semester AND r.idpelajaran = $pelajaran AND r.aktif = 1 ORDER BY koderpp";

$query_aturan = "SELECT r.replid, r.rpp FROM rpp r, kelas k WHERE r.idtingkat = k.idtingkat AND k.replid = $kelas AND r.idsemester = $semester AND r.idpelajaran = $pelajaran AND r.aktif = 1 ORDER BY koderpp";

$result_aturan = QueryDb($query_aturan);
if (!mysql_num_rows($result_aturan)==0){ ?>

<table class="tab" id="table" border="1" style="border-collapse:collapse" width="100%" align="left" bordercolor="#000000">
<!-- TABLE CONTENT -->
    
<tr height="30">    	
    <td width="100%" class="header" align="center">RPP</td>
</tr>	
<? 
	$i=0;
	$cnt = 0;
	while ($row_aturan=@mysql_fetch_array($result_aturan)){
		if ($i>=5)
			$i=0;
		
?>
<tr>   	
    <td align="center" height="25" onclick="pilih(<?=$kelas?>,<?=$row_aturan['replid']?>)" style="cursor:pointer"><u><b><?=$row_aturan['rpp']?></b></u>
    </td>
</tr>
<!-- END TABLE CONTENT -->
<?
  		$i++;
 	} 
?>	
</table>
<script language='JavaScript'>
	Tables('table', 1, 0);
</script>
<? 
	CloseDb(); 
	} else { 
?>
<table width="100%" border="0" align="center">          
<tr>
    <td align="center" valign="middle" height="200">
    <font size = "2" color ="red"><b>Tidak ditemukan adanya data. <br /><br />Tambah rencan program pembelajaran untuk tingkat <?=$namakelas?> dan pelajaran <?=$namapelajaran?> di menu Rencana Program Pembelajaran pada bagian Guru & Pelajaran. </b></font>
    </td>
</tr>
</table> 
<? } ?> 
</body>
</html>