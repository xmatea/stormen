import random
adjektiv= ["Glatt",
"Fysisk",
"Mekanisk",
"Mekanikkens",
"Moderne",
"Dødelig",
"Utrydnindstruet",
"Klassisk",
"Endelig",
"Varmedødens",
"Århundrets",
"Livets",
"Teknologisk",
"Alternativ",
"Syntetisk",
"Kamuflert",
"Betent",
"Gudsforlatt",
"Kjemisk",
"Naturlig",
"Unaturlig",
"Biologisk",
"Biologiens",
"Dødens",
"Stjernenes",
"Berømt",
"Antikk",
"Skjult",
"Hemmelig",
"Mystisk",
"Berggrunnens",
"Geologisk",
"Eksperimentell",
"Super",
"Evig",
"Udødelig",
"Uunngåelig",
"Hodeknusende",
"Tidens",
"Tidenes",
"Slitt",
"Ivrig",
"Irritert",
"Nedstemt",
"Deprimert",
"Uhyggelig",
"Drepende",
"Kald",
"Maskinell",
"Falsk"]

substativ = ["Reke",
"Stol ",
"Salat",
"Sekk ",
"Kniv ",
"Sparkesykkel",
"Elbil",
"Glassbur ",
"Stein",
"Fjell",
"Bok",
"Telefon",
"Vott",
"Ski",
"Paraply",
"Undertøy",
"Seng",
"Rotte",
"Bavian",
"Banan",
"Mamma",
"Vaffel",
"Arm",
"Ridder",
"Kropp",
"Sopp",
"Bekk",
"Sjakk ",
"Myr",
"Pappa",
"Mangfold",
"Pizza",
"Bombe",
"Legende",
"Katt",
"Sykepleier",
"Øyvind ",
"Bil ",
"Brus",
"Kake",
"Videospill",
"Kuber",
"Projekt",
"Torsk",
"Sei",
"Depresjon",
"Schizofreni",
"Galskap",
"Helvete",
"Jente"]

tittel = []
file = open("a.txt", 'a')
for s in substativ:
    for a in adjektiv:
        tittel.append(f"{a} {s}")

random.shuffle(tittel)

for t in tittel:
    print(t)
    file.write(f"{t}\n")
