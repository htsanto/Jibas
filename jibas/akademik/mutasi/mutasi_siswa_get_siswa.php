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
require_once('../include/theme.php');
require_once('../include/db_functions.php');
require_once('../library/departemen.php');
OpenDb();
$departemen=$_REQUEST[departemen];
$nis=$_REQUEST[nis];
$nama=$_REQUEST[nama];
if ($nis<>"" && $nama=="")
	$sql="SELECT * FROM jbsakad.siswa WHERE nis LIKE '%$nis%' ORDER BY idkelas,nama";
if ($nama<>"" && $nis=="")
	$sql="SELECT * FROM jbsakad.siswa WHERE nama LIKE '%$nama%' ORDER BY idkelas,nama";
if ($nama<>"" && $nis<>"")
	$sql="SELECT * FROM jbsakad.siswa WHERE nama LIKE '%$nama%', AND nis LIKE '%$nis%' ORDER BY idkelas,nama";	

$result=QueryDb($sql);

?>
kjhsjdfjuygkjgkhgfukhg
<table width="100%" border="0" id="tablecari" class="tab">
<tr>
    <td  height="30" align="center"  class="header" >No.</td>
    <td  height="30" align="center"  class="header">NIS</td>
    <td  height="30" align="center"  class="header">Nama</td>
    <td  height="30" align="center"  class="header">Kelas</td>
</tr>
<?
if (@mysql_num_rows($result)>0){
$i=1;
while ($row=@mysql_fetch_array($result)){
//$font2="</font>";
if (($i%2)<>0){
	$bg="bgcolor='#C0C0C0'";
	} else {
	$bg="";
	}

?>
<tr>
    <td align="center" <?=$bg?>><?=$i?></td>
    <td align="center" <?=$bg?>><?=$row[nis]?></td>
    <td  <?=$bg?>><?=$row[nama]?></td>
    <td  <?=$bg?>><?=$row[idkelas]?></td>
</tr>
<?
$i++;
}
} else {

?>
<tr>
    <td colspan="4" align="center" height="25">Tidak ditemukan data</td>
</tr>
<?

}
?>
</table>
<script language="javascript">
		Tables('tablecari', 1, 0);
	</script>