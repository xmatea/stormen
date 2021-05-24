<?php
#definerer spørringer som blir ofte brukte
$bøker_forfatterliste = "SELECT
bok.id,
bok.ISBN,
bok.tittel,
bok.kategori,
bok.forlag,
bok.status,
dewey.idDewey,
dewey.tittel as kategorinavn,
GROUP_CONCAT(distinct C.fornavn, ' ', C.etternavn ORDER BY C.fornavn ASC SEPARATOR ', ') forfatternavn,
GROUP_CONCAT(distinct C.idforfatter ORDER BY C.idforfatter ASC SEPARATOR ', ') forfatterid
FROM bok
JOIN dewey ON bok.kategori=dewey.idDewey
JOIN forfatter_has_bok on forfatter_has_bok.bok_id = bok.id
JOIN forfatter on forfatter.idforfatter=forfatter_has_bok.forfatter_idforfatter
LEFT JOIN forfatter_has_bok B
ON bok.id=B.bok_id
LEFT JOIN forfatter C
ON B.forfatter_idforfatter=C.idforfatter";

$utlånerliste = "SELECT bok.id, bok.tittel, personnummer, CONCAT(fornavn, ' ', etternavn) utlåner, utlånsdato
from utlån
join utlåner on utlånerid=personnummer
join bok on bokid = id";
?>
