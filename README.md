# Instrukcja kompilacji i testowego uruchomienia aplikacji w środowisku lokalnym

Aplikacja została zaprojektowana w sposób umożliwiający jej uruchomienie w środowisku lokalnym z wykorzystaniem konteneryzacji Docker. System składa się z trzech głównych części:

- back-endu opartego o framework Laravel,
- front-endu w technologii Vue,
- aplikacji mobilnej napisanej w Flutterze, utrzymywanej w oddzielnym repozytorium.

## Wymagania wstępne

Do poprawnego uruchomienia projektu wymagane jest zainstalowanie następujących narzędzi:

- Docker oraz Docker Compose,
- Git,
- Make,
- Node.js,
- Flutter SDK.

## Pobranie projektu

W pierwszym kroku należy sklonować repozytorium z systemu kontroli wersji Git:

```bash
git clone https://github.com/AKasprzy/BookNook
```

Następnie należy utworzyć lokalny plik konfiguracyjny środowiska:

```bash
cp .env.example .env
```

Plik ten zawiera wszystkie niezbędne zmienne konfiguracyjne wymagane do uruchomienia aplikacji.

## Budowa i uruchomienie środowiska

Środowisko uruchamiane jest za pomocą Docker Compose, a proces budowy i startu został zautomatyzowany przy użyciu Makefile.

### Budowa obrazów kontenerów

```bash
make build
```

### Uruchomienie wszystkich usług

```bash
make run
```

### Zatrzymanie środowiska

```bash
make stop
```

## Migracje bazy danych

Po uruchomieniu kontenerów należy wykonać migracje bazy danych:

```bash
make migrate
```

W przypadku potrzeby pełnego odtworzenia struktury bazy wraz z danymi testowymi można użyć:

```bash
make seed
```

## Uruchomienie warstwy frontendowej

Frontend aplikacji oparty o Vue uruchamiany jest w kontenerze Node.js:

```bash
make dev
```

Polecenie to instaluje zależności oraz uruchamia tryb deweloperski aplikacji frontendowej.
