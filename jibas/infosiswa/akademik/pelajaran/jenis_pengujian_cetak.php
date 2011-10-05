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
require_once('../include/getheader.php');
require_once('../include/db_functions.php');

$id = $_REQUEST['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="../style/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cetak Status Guru</title>
</head>

<body>
<table border="0" cellpadding="10" cellpadding="5" width="780" align="left">
<tr><td align="left" valign="top">

<? include("../library/headercetak.php") ?>

<center>
  <font size="3"><strong>DATA JENIS PENGUJIAN</strong></font><br />
 </center>
<br />
    <br />
<?
OpenDb();
		$sql = "SELECT j.replid,j.jenisujian,j.idpelajaran,j.keterangan,p.replid,p.nama,p.departemen FROM jenisujian j, pelajaran p WHERE j.idpelajaran = $id AND p.replid=j.idpelajaran ";   
		$result = QueryDb($sql);
		$cnt = 0;
		if ($row = @mysql_fetch_row($result)) {
		?><strong>
Pelajaran  : <?=$row[5]?>
<br />
Departemen : <?=$row[6]?>
<br /><br /><br /></strong>
<? 
												  }
CloseDb(); 
?>
<table class="tab" id="table" border="1" cellpadding="2" style="border-collapse:collapse" cellspacing="2" width="100%" align="left">
    <!-- TABLE CONTENT -->
    <tr height="30">
    	<td width="4%" class="header" align="center">No</td>
        <td width="30%" class="header" align="center">Jenis Pengujian</td>
        <td width="47%" class="header" align="center">Keterangan</td>
    </tr>
    
     <?
		OpenDb();
		$sql = "SELECT j.replid,j.jenisujian,j.idpelajaran,j.keterangan,p.replid,p.nama,p.departemen FROM jenisujian j, pelajaran p WHERE j.idpelajaran = $id AND j.idpelajaran = p.replid ORDER BY p.nama";   
		$result = QueryDb($sql);
		$cnt = 0;
		while ($row = @mysql_fetch_row($result)) {
		?>
    <tr>   	
       	<td align="center"><?=++$cnt ?></td>
        <td align="center"><?=$row[1]?></td>
        <td><?=$row[3]?></td>        
    </tr>
<?	} 
	CloseDb(); ?>	
    
    <!-- END TABLE CONTENT -->
    </table>

</td></tr></table>
</body>
<script language="javascript">
window.print();
</script>
</html>