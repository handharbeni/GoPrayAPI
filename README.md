# GoPrayAPI

[URL Routing]

[User/Anak]
# GET User detail
/v1/users/self/?access_token={AT}

# GET Timeline
/v1/users/self/timeline?access_token={AT}

# GET Pesan
/v1/users/self/pesan?access_token={AT}

# GET Kerabat
/v1/users/self/kerabat?access_token={AT}

# POST Login
=> email
=> password
/v1/users/self/login

# POST Daftar
=> nama
=> email
=> password
/v1/users/self/daftar

# POST Kerabat (INSERT)
=> access_token
=> metode ('insert')
=> kerabat
=> nama
=> email
=> gambar
=> no_hp

# POST Timeline
=> access_token
=> id_aktivitas
=> id_ibadah
=> tepmat
=> bersama
=> gambar
=> point
=> tanggal
=> jam

[Master]
# GET Sholat
/v1/master/sholat?access_token={AT}

# GET Puasa
/v1/master/puasa?access_token={AT}

# GET Doa
/v1/master/doa?access_token={AT}

# GET Aktivitas
/v1/master/aktivitas?access_token={AT}

# GET Jadwal Sholat
/v1/master/aktivitas?access_token={AT}&timezone={tz}

# GET Stiker
/v1/master/stiker?access_token={AT}

# GET Paket Stiker
/v1/master/paketstiker?access_token={AT}

# on Building

Code By reksarw@gmail.com
