<?php
$h2 = '$2y$10$RvIqqihKT1t/iiJRiLmYru4Ml8uVA5YAm84Q9fOXZgwjZIx1zB/8W';
$h3 = '$2y$10$O.tERJleO6PTT/fRb.VqjOS49Tw.uRn60RtxMk1eF0h5NuFccK04a';
echo password_verify('juri123', $h2) ? "juri: juri123\n" : "";
echo password_verify('peserta123', $h3) ? "peserta: peserta123\n" : "";
