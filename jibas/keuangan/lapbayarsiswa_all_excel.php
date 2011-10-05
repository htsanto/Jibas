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
require_once('include/db_functions.php');
require_once('include/getheader.php'); 
require_once('library/jurnal.php');
require_once('library/repairdatajtt.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera  
header('Content-Type: application/x-msexcel'); // Other browsers  
header('Content-Disposition: attachment; filename=Data_Pembayaran_Siswa_'.$nis.'.xls');
header('Expires: 0');  
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$idtahunbuku = $_REQUEST['idtahunbuku'];
$nis = $_REQUEST['nis'];
$tanggal1 = $_REQUEST['tanggal1'];
$tanggal2 = $_REQUEST['tanggal2'];

OpenDb();
$sql = "SELECT s.nama, k.kelas, t.tingkat, t.departemen 
          FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
			WHERE s.nis = '$nis' AND s.idkelas = k.replid AND k.idtingkat = t.replid";
$result = QueryDb($sql);
$row = mysql_fetch_row($result);
$namasiswa = $row[0];
$kelas = $row[1];
$tingkat = $row[2];
$departemen = $row[3];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JIBAS KEU [Laporan Pembayaran Per Siswa]</title>
</head>

<body>
<center><font size="4"><strong>DATA PEMBAYARAN SISWA</strong></font><br /> </center><br /><br />

<?

?>
<table border="0">
<tr>
	<td><strong>Siswa </strong></td>
    <td><strong>: <?=$nis . " - " . $namasiswa?></strong></td>
</tr>
<tr>
	<td><strong>Kelas </strong></td>
    <td><strong>: <?=$tingkat." - ".$kelas ?></strong></td>
</tr>
<tr>
	<td><strong>Tanggal </strong></td>
    <td><strong>: <?=LongDateFormat($tanggal1) . " s/d " . LongDateFormat($tanggal2) ?></strong></td>
</tr>
</table>
<br />

<table border="1" style="border-collapse:collapse" width="100%" bordercolor="#000000">
<?
$sql = "SELECT DISTINCT b.replid AS id, b.besar, b.lunas, b.keterangan, d.nama 
          FROM besarjtt b, penerimaanjtt p, datapenerimaan d 
		   WHERE p.idbesarjtt = b.replid AND b.idpenerimaan = d.replid AND b.nis='$nis' AND b.info2='$idtahunbuku'
			  AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' ORDER BY nama";
$result = QueryDb($sql);
while ($row = mysql_fetch_array($result)) {
	$idbesarjtt = $row['id'];
	$namapenerimaan = $row['nama']; 
	$besar = $row['besar'];
	$lunas = $row['lunas'];
	$keterangan = $row['keterangan'];
	
	$sql = "SELECT SUM(jumlah) FROM penerimaanjtt WHERE idbesarjtt = $idbesarjtt";
	$result2 = QueryDb($sql);
	$pembayaran = 0;
	if (mysql_num_rows($result2)) {
		$row2 = mysql_fetch_row($result2);
		$pembayaran = $row2[0];
	};
	$sisa = $besar - $pembayaran;
	
	$sql = "SELECT jumlah, DATE_FORMAT(tanggal, '%d-%b-%Y') AS ftanggal FROM penerimaanjtt WHERE idbesarjtt=$idbesarjtt ORDER BY tanggal DESC LIMIT 1";
	$result2 = QueryDb($sql);
	$byrakhir = 0;
	$tglakhir = "";
	if (mysql_num_rows($result2)) {
		$row2 = mysql_fetch_row($result2);
		$byrakhir = $row2[0];
		$tglakhir = $row2[1];
	};	?>
    <tr height="35">
        <td colspan="4" bgcolor="#99CC00"><font size="2" face="Arial"><strong><em><?=$namapenerimaan?></em></strong></font></td>
  </tr>    
    <tr height="25">
        <td width="20%" bgcolor="#CCFF66"><font size="2" face="Arial"><strong>Total Bayaran</strong> </font></td>
      <td width="15%" bgcolor="#FFFFFF" align="right"><font size="2" face="Arial">
      <?=$besar ?>
      </font></td>
      <td width="22%" bgcolor="#CCFF66" align="center"><font size="2" face="Arial"><strong>Pembayaran Terakhir</strong></font></td>
      <td width="43%" bgcolor="#CCFF66" align="center"><font size="2" face="Arial"><strong>Keterangan</strong></font></td>
  </tr>
    <tr height="25">
        <td bgcolor="#CCFF66"><font size="2" face="Arial"><strong>Jumlah Pembayaran</strong> </font></td>
      <td bgcolor="#FFFFFF" align="right"><font size="2" face="Arial">
      <?=$pembayaran ?>
      </font></td>
      <td bgcolor="#FFFFFF" align="center" valign="top" rowspan="2"><font size="2" face="Arial">
      <?=$byrakhir . "<br><i>" . $tglakhir . "</i>" ?> 
      </font></td>
      <td bgcolor="#FFFFFF" align="left" valign="top" rowspan="2"><font size="2" face="Arial">
      <?=$keterangan ?>
      </font></td>
  </tr>
    <tr height="25">
        <td bgcolor="#CCFF66"><font size="2" face="Arial"><strong>Sisa Bayaran</strong> </font></td>
      <td bgcolor="#FFFFFF" align="right"><font size="2" face="Arial">
      <?=$sisa ?>
      </font></td>
  </tr>
    <tr height="3">
        <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
  </tr>
<? 
} //while iuran wajib

$sql = "SELECT DISTINCT p.idpenerimaan, d.nama 
          FROM penerimaaniuran p, datapenerimaan d, jurnal j
		   WHERE p.idpenerimaan = d.replid AND j.replid = p.idjurnal AND j.idtahunbuku = $idtahunbuku
			  AND p.nis='$nis' AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' ORDER BY nama";
$result = QueryDb($sql);
while ($row = mysql_fetch_array($result)) {
	$idpenerimaan = $row['idpenerimaan'];
	$namapenerimaan = $row['nama'];
	
	$sql = "SELECT SUM(jumlah) FROM penerimaaniuran WHERE idpenerimaan=$idpenerimaan AND nis='$nis'";
	$result2 = QueryDb($sql);
	$pembayaran = 0;
	if (mysql_num_rows($result2)) {
		$row2 = mysql_fetch_row($result2);
		$pembayaran = $row2[0];
	};

	$sql = "SELECT jumlah, DATE_FORMAT(tanggal, '%d-%b-%Y') AS ftanggal FROM penerimaaniuran WHERE idpenerimaan=$idpenerimaan AND nis='$nis' ORDER BY tanggal DESC LIMIT 1";
	$result2 = QueryDb($sql);
	$byrakhir = 0;
	$tglakhir = "";
	if (mysql_num_rows($result2)) {
		$row2 = mysql_fetch_row($result2);
		$byrakhir = $row2[0];
		$tglakhir = $row2[1];
	};	
?>
 	<tr height="35">
        <td colspan="4" bgcolor="#99CC00"><font size="2" face="Arial"><strong><em><?=$namapenerimaan?></em></strong></font></td>
  </tr>  
   	<tr height="25">
        <td width="22%" bgcolor="#CCFF66" align="center"><font size="2" face="Arial"><strong>Total Pembayaran</strong> </font></td>
      <td width="22%" bgcolor="#CCFF66" align="center"><font size="2" face="Arial"><strong>Pembayaran Terakhir</strong></font></td>
      <td width="50%" colspan="2" bgcolor="#CCFF66" align="center"><font size="2" face="Arial"><strong>Keterangan</strong></font></td>
  </tr>
    <tr height="25">
        <td bgcolor="#FFFFFF" align="center"><font size="2" face="Arial">
        <?=$pembayaran ?>
        </font></td>
      <td bgcolor="#FFFFFF" align="center"><font size="2" face="Arial">
      <?=$byrakhir . "<br><i>" . $tglakhir . "</i>" ?>
      </font></td>
      <td colspan="2" bgcolor="#FFFFFF" align="left">&nbsp;</td>
  </tr>
    <tr height="3">
        <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
  </tr>
<?
} //while iuran sukarela
?>
</table>

<?
CloseDb();
?>

</table>

</body>
</html>
<script language="javascript">window.print();</script>