# Stormen Bibliotek-oppgave
Av Matea og William

## Teknisk om siden:
- Administratorbrukere kan kun registreres eksternt og for å komme inn på admin-sidene må du logge inn med:

```
brukernavn: admin
passord: rootpass
```

- Nesten all data i prosjektet har blitt generert ved hjelp av ordlister. Ettersom vi fikk elever med til å finne ord i ordlistene, kan det hende noen av ordene eller ordsammensetningene kan være upassende... beklager det heh... Opphavet til den genererte koden kan finnes i /generated
- SQL-dataen inkludert skjema og modell finnes i /sql
- Kategorier lagres med dewey-indekser slik Stormen også gjør, for å notere hylleplass. En egen dewey-tabell binder disse indeksene med kategorinavn.
- Alle søkefelt benytter %like%-funksjonen i SQL, slik at du kan søke med ufullstendige søkeord i alle søkefelt.
