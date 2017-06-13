# GoPrayAPI

[User]
# GET User detail
/users/self/?access_token={AT}

# GET Timeline
/users/self/timeline?access_token={AT}

# GET Pesan
/users/self/pesan?access_token={AT}

# GET Kerabat
/users/self/kerabat?access_token={AT}

# POST Login
=> email
=> password
/users/self/login

# POST Daftar
=> nama
=> email
=> password
/users/self/daftar

# POST Kerabat
=> access_token
=> email
/users/self/Kerabat

# POST Timeline
=> access_token
=> id_aktivitas
=> id_ibadah
=> tempat (optional)
=> bersama (optional)
=> nominal (optional)
=> gambar (optional)
=> point
/users/self/timeline

# POST Meme
=> access_token
=> text
=> gambar (optional)
/users/self/meme

# POST Setting Profile
=> access_token
=> method
(method == 1)
=> nama (optional)
=> email (optional)
(method == 2)
=> gambar
(method == 3)
=> password
/users/self/profile

# POST Delete Timeline
=> access_token
=> id_timeline
/users/self/deletetimeline

# POST delete meme
=> access_token
=> id_meme
/users/self/deletememe

[Master]
# GET Sholat
/master/sholat?access_token={AT}

# GET Puasa
/master/puasa?access_token={AT}

# GET Doa
/master/doa?access_token={AT}

# GET Aktivitas
/master/aktivitas?access_token={AT}

# GET Jadwal Sholat
/master/aktivitas?access_token={AT}&timezone={tz}

# GET Stiker
/master/stiker?access_token={AT}

# GET Paket Stiker
/master/paketstiker?access_token={AT}

# GET Message
/master/pesan?access_token{AT}

# POST JadwalSholat (Sinkron)
=> method (monthly / yearly)
/master/jadwalsholat

# POST Message
=> access_token
=> pesan / gambar
/master/pesan

# POST Paket Stiker
=> nama_paket
=> harga
/master/paketstiker

# POST Stiker
=> nama_stiker
=> cover (gambar)
=> harga
/master/stiker

# POST Child Stiker
=> kd_stiker
=> gambar
=> nomer
/master/childstiker

[Orang Tua]
# GET Parent Detail 
/users/parent/?access_token={AT}

# GET List Kerabat
/users/parent/list?opsi=kerabat&access_token={AT}

# POST Daftar
=> kerabat
=> nama
=> email
=> no_hp
=> password
/users/parent/daftar

# Post Login
=> email
=> password
/users/parent/login

# POST Setting Profile
=> access_token
=> method
(method == 1)
=> kerabat (optional)
=> nama (optional)
=> no_hp (optional)
(method == 2)
=> gambar 
(method == 3)
=> password
(method == 4)
=> kerabat (optional)
=> nama (optional)
=> no_hp (optional)
=> gambar (optional)
=> password (optional)
/users/parent/profile

# on Building

Code By reksarw@gmail.com