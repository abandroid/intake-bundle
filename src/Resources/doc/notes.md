# Intake

* Per niveau kan bepaald worden hoeveel teksten aangemaakt worden.
* Voor ieder niveau vul je een grens in voor twijfel en een grens voor falen.
* Verschillende antwoorden kunnen gescheiden met een komma ingevoerd worden.
  * De huidige status wordt bewaard in de database (dus niet in de sessie).
  * Bij per ongeluk afsluiten van de browser kun je inloggen om verder te gaan.
* Bij opslaan van een niveau moet je extra bevestigen.
  * Om per ongeluk beëindigen uit te sluiten.
  * Ongeacht of je alles hebt ingevuld.
* Na beëindigen van een niveau kun je deze niet meer bewerken.
* Alle tekens anders dan a-z en 0-9 aan begin en eind van een antwoord worden genegeerd bij het valideren tegen de toegestane antwoorden.
* Bij twijfel of falen ga je door naar het volgende niveau.
  * Dit is dan wel het laatste niveau, ongeacht succes, twijfel of falen.
* De afnemer krijgt altijd aanvulzinnen bij de laatste tekst, ongeacht het resultaat.
* Per intake kan het adres voor mailen van de resultaten ingesteld worden.
* Na het afronden van een intake wordt de volgende informatie gestuurd.
  * Per niveau het aantal vragen, fouten en oordeel: behaald, twijfel of gezakt.
