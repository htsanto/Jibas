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
require_once('include/rupiah.php');
require_once('include/sessioninfo.php');
require_once('include/db_functions.php');
require_once('include/getheader.php');
$id = $_REQUEST['id'];
if ($_REQUEST['status'] == 'calon') {
	$sql = "SELECT p.replid AS id, c.nopendaftaran, c.nama, j.nokas, j.transaksi, date_format(p.tanggal, '%d-%b-%Y') as tanggal, p.keterangan, p.jumlah, p.petugas, j.idtahunbuku FROM penerimaaniurancalon p, jurnal j, jbsakad.calonsiswa c WHERE j.replid = p.idjurnal AND p.idcalon = c.replid AND p.replid = $id";
} else {
	$sql = "SELECT p.replid AS id, p.nis, s.nama, j.nokas, j.transaksi, date_format(p.tanggal, '%d-%b-%Y') as tanggal, p.keterangan, p.jumlah, p.petugas, j.idtahunbuku FROM penerimaaniuran p, jurnal j, jbsakad.siswa s WHERE j.replid = p.idjurnal AND p.nis = s.nis AND p.replid = $id";
}
OpenDb();
$result = QueryDb($sql);
$row = mysql_fetch_row($result);

$nokas = $row[3];
$transaksi = $row[4];
$tanggal = $row[5];
$jumlah = $row[7];
$petugas = $row[8];
$nis = $row[1];
$nama = $row[2];
$idtahunbuku = $row[9];
$sql = "SELECT date_format(now(), '%d %M %Y') as tanggal";
$result = QueryDb($sql);
$row = mysql_fetch_row($result);
$tglcetak = $row[0];

$sql = "SELECT departemen FROM tahunbuku WHERE replid=$idtahunbuku";
$result = QueryDb($sql);
$row = @mysql_fetch_array($result);
$departemen=$row[departemen];
CloseDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="style/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JIBAS KEU [Kuitansi Pembayaran]</title>
</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<table border="0" cellpadding="5" cellspacing="0" width="740" align="center">
<tr><td>
<?=getHeader($departemen)?>
<? for($i = 0; $i < 2; $i++) { ?>
<tr>
<td colspan="2" align="center" valign="top">
	<table border="0" cellpadding="2" cellspacing="0" width="90%" align="center">
    <tr>
    	<td align="right"><font size="2"><strong>No. <?=$nokas ?></strong></font></td>
    </tr>
    <tr><td align="center">
    	<br />
        <font size="3"><strong>KUITANSI PEMBAYARAN</strong></font>
    </td></tr>
    <tr><td align="left">
    	<br />Telah terima dari:<br />
        <table cellpadding="3">
        <tr>
        	<td width="20">&nbsp;</td>
        	<td width="90"><? if ($_REQUEST["status"] == "calon") echo  "No Pendaftaran"; else echo  "N I S"; ?></td>
            <td>:&nbsp;<strong><?=$nis ?></strong></td>
        </tr>
        <tr>
        	<td width="20">&nbsp;</td>
        	<td width="80">Nama</td>
            <td>:&nbsp;<strong><?=$nama ?></strong></td>
        </tr>
        <tr>
        	<td colspan="2">Uang sejumlah</td>
            <td background="images/bkmoney.png">
            <font size="2"><strong><em>
            <?=FormatRupiah($jumlah) ?>
            </em></strong></font>
            </td>
        </tr><p>
        <tr>
        	<td colspan="2">&nbsp;</td>
        	<td background="images/bkmoney.png">
            (<font size="2"><strong><em>
            <?=KalimatUang($jumlah) ?>
            </em></strong></font>)
            </td>
        </tr>
        <tr>
        	<td colspan="3">Untuk <?=$transaksi ?></td>
        </tr>
        </table>
        
        <table border="0" width="100%" cellpadding="2" cellspacing="2">
        <tr height="160">
        	<td width="65%">
            <fieldset>
            <legend><em><strong>Keterangan</strong></em></legend>
            <table border="0" width="100%">
            <tr height="80"><td valign="top">&nbsp;
            
            </td></tr>
            </table>
            </fieldset>
            </td>
            <td align="center">
            <?=$G_LOKASI . ", " . $tglcetak ?><br />
            Penerima<br /><br /><br /><br /><br /><br />
            ( <?=getUserName() ?> )
            </td>
        </tr>
        </table>
    </td></tr>
    </table>
</td></tr>
</table>

</td></tr>
</table>
<? if ($i == 0) { ?>
<hr width="750" style="border-style:dashed; line-height:1px; color:#999999;" />
<? } ?>

<? } //for ?>
</body>
<script language="javascript">
window.print();
</script>
</html>