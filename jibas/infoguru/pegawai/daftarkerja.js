function TambahS()
{
    var nip = document.getElementById('nip').value;
	var addr = "kerjaadd.php?nip="+nip;
    newWindow(addr, 'TambahKerja','450','250','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function Ubah(id)
{
	var addr = "kerjaedit.php?id="+id;
    newWindow(addr, 'UbahKerja','450','285','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function Refresh()
{
    var nip = document.getElementById('nip').value;
	document.location.href = "daftarkerja.php?nip="+nip;
}

function Hapus(id)
{
	if (confirm("Apakah anda yakin akan menghapus data ini?"))
	{
		var nip = document.getElementById('nip').value;
		document.location.href = "daftarkerja.php?id="+id+"&op=mnrmd2re2dj2mx2x2x3d2s33&nip="+nip;
	}
}

function ChangeLast(id)
{
	if (confirm('Apakah anda yakin akan mengubah data ini menjadi data riwayat pekerjaan terakhir?'))
    {
        var nip = document.getElementById('nip').value;
		document.location.href = "daftarkerja.php?id="+id+"&op=cn0948cm2478923c98237n23&nip="+nip;
    }
}

function Cetak()
{
    var nip = document.getElementById('nip').value;
	newWindow('daftarkerja_cetak.php?nip='+nip, 'CetakDaftarKerja','790','650','resizable=1,scrollbars=1,status=0,toolbar=0')
}
