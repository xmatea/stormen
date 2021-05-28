<?php
# DENNE FILEN INNEHOLDER MER ELLER MINDRE ALLE SPØRRINGENE SOM BLIR BRUKT I PROSJEKTET
# nydelige spørringer....

# setter sammen forfatternavn til et felt med alle forfatternavn (1 eller 2 forfattere) separert med komma.
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

# gjør den samme sammensetningen her, men med utlånernavn i stedet for forfattere.
$utlånerliste = "SELECT
bok.id,
bok.ISBN,
bok.tittel,
bok.kategori,
bok.forlag,
dewey.idDewey,
dewey.tittel as kategorinavn,
utlån.utlånsdato,
utlån.utlånerid as personnummer,
utlåner.fornavn,
utlåner.etternavn,
GROUP_CONCAT(distinct C.fornavn, ' ', C.etternavn ORDER BY C.fornavn ASC SEPARATOR ', ') forfatternavn
FROM utlån
JOIN bok on bok.id = bokid
JOIN dewey ON bok.kategori=dewey.idDewey
JOIN utlåner ON utlåner.personnummer = utlån.utlånerid
JOIN forfatter_has_bok on forfatter_has_bok.bok_id = bok.id
JOIN forfatter on forfatter.idforfatter=forfatter_has_bok.forfatter_idforfatter
LEFT JOIN forfatter_has_bok B
ON bok.id=B.bok_id
LEFT JOIN forfatter C
ON B.forfatter_idforfatter=C.idforfatter";
?>
